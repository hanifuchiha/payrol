<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_sso {
    const BASE = 'https://billing.broadbandairlink.com/web/keuangan/sso/index.php/';
    const CALLBACK = 'https://billing.broadbandairlink.com/web/keuangan/payrol/index.php/auth/sso_callback';

    private function central() {
        $ci =& get_instance();
        $config = $ci->db;
        return $ci->load->database(array(
            'hostname'=>$config->hostname, 'username'=>$config->username,
            'password'=>$config->password, 'database'=>'sso_central',
            'dbdriver'=>'mysqli', 'pconnect'=>FALSE, 'db_debug'=>FALSE,
            'char_set'=>'utf8mb4', 'dbcollat'=>'utf8mb4_unicode_ci', 'save_queries'=>FALSE
        ), TRUE);
    }

    public function account($subject, $hash) {
        $db = $this->central();
        if (!$db || !$db->conn_id) throw new RuntimeException('SSO tidak tersedia.');
        $q = $db->query("SELECT u.id,u.username,u.name FROM users u
            JOIN user_client_access a ON a.user_id=u.id AND a.is_active=1 AND a.role_slug='administrator'
            JOIN clients c ON c.id=a.client_id AND c.client_id='payrol' AND c.is_active=1
            JOIN access_tokens t ON t.user_id=u.id AND t.client_id=c.id
                AND t.token_hash=? AND t.revoked_at IS NULL AND t.expires_at>=NOW()
            WHERE u.id=? AND u.status='active' LIMIT 1", array($hash,(int)$subject));
        if (!$q) throw new RuntimeException('Pemeriksaan akses SSO gagal.');
        return $q->row_array();
    }

    public function enforce() {
        $ci =& get_instance();
        $s = $ci->session->userdata('payroll_sso');
        if (!is_array($s)) {
            $ci->session->unset_userdata(array('admin_id','admin_name','admin_username'));
            redirect('auth/login'); exit;
        }
        try { $u = $this->account($s['sub'] ?? 0, $s['token_hash'] ?? ''); }
        catch (Throwable $e) { show_error('Pemeriksaan akses SSO sementara tidak tersedia.',503); exit; }
        if (!$u) {
            $ci->session->sess_destroy();
            show_error('Akses Payroll berakhir atau tidak diizinkan oleh SSO. Silakan login SSO kembali.',403); exit;
        }
        $ci->session->set_userdata(array('admin_id'=>(int)$u['id'],'admin_name'=>$u['name'],'admin_username'=>$u['username']));
    }

    public function start() {
        $ci =& get_instance();
        $state = bin2hex(random_bytes(32));
        $verifier = rtrim(strtr(base64_encode(random_bytes(48)), '+/', '-_'), '=');
        $ci->session->set_userdata('payroll_sso_pending',array('state'=>$state,'verifier'=>$verifier,'created_at'=>time()));
        $query = http_build_query(array('client_id'=>'payrol','redirect_uri'=>self::CALLBACK,
            'response_type'=>'code','scope'=>'openid profile','state'=>$state,
            'code_challenge'=>rtrim(strtr(base64_encode(hash('sha256',$verifier,TRUE)), '+/', '-_'),'='),
            'code_challenge_method'=>'S256'));
        session_write_close();
        redirect(self::BASE.'authorize?'.$query, 'location',303);
    }

    public function callback() {
        $ci =& get_instance();
        $pending = $ci->session->userdata('payroll_sso_pending');
        $ci->session->unset_userdata('payroll_sso_pending');
        $state = (string)$ci->input->get('state');
        $code = (string)$ci->input->get('code');
        if (!is_array($pending) || $code==='' || $state==='' || time()-(int)$pending['created_at']>600
            || !hash_equals((string)$pending['state'],$state)) {
            show_error('Sesi login SSO tidak valid atau sudah berakhir. Mulai login kembali.',400); return;
        }
        session_write_close();
        try {
            $token = $this->http('token',array('grant_type'=>'authorization_code','client_id'=>'payrol',
                'redirect_uri'=>self::CALLBACK,'code'=>$code,'code_verifier'=>$pending['verifier']));
            if (empty($token['access_token'])) throw new RuntimeException('Token tidak tersedia.');
            $profile = $this->http('userinfo',NULL,$token['access_token']);
            if (($profile['client_id']??'')!=='payrol' || ($profile['role']??'')!=='administrator'
                || !ctype_digit((string)($profile['sub']??''))) throw new RuntimeException('Profil tidak diizinkan.');
            $hash = hash('sha256',$token['access_token']);
            $u = $this->account((int)$profile['sub'],$hash);
            if (!$u) throw new RuntimeException('Akses tidak aktif.');
        } catch (Throwable $e) {
            log_message('error','Payroll SSO callback failed: '.$e->getMessage());
            show_error('Login SSO Payroll gagal. Mulai login kembali atau periksa izin Payroll di SSO.',403); return;
        }
        session_start();
        $ci->session->sess_regenerate(TRUE);
        $ci->session->set_userdata(array('admin_id'=>(int)$u['id'],'admin_name'=>$u['name'],
            'admin_username'=>$u['username'],'payroll_sso'=>array('sub'=>(int)$u['id'],'token_hash'=>$hash)));
        redirect('', 'location',303);
    }

    private function http($endpoint,$post=NULL,$token=NULL) {
        $url = self::BASE.$endpoint;
        $ch = curl_init($url);
        $headers = array('Accept: application/json');
        if ($token!==NULL) $headers[]='Authorization: Bearer '.$token;
        if ($post!==NULL) {
            curl_setopt($ch,CURLOPT_POST,TRUE);
            curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($post));
            $headers[]='Content-Type: application/x-www-form-urlencoded';
        }
        curl_setopt_array($ch,array(CURLOPT_HTTPHEADER=>$headers,CURLOPT_RETURNTRANSFER=>TRUE,
            CURLOPT_FOLLOWLOCATION=>FALSE,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>15,
            CURLOPT_SSL_VERIFYPEER=>TRUE,CURLOPT_SSL_VERIFYHOST=>2));
        require_once dirname(APPPATH,2).'/shared/Sso_backchannel.php';
        AirlinkSsoBackchannel::configure($ch,$url,$headers);
        $raw = curl_exec($ch); $status = (int)curl_getinfo($ch,CURLINFO_HTTP_CODE); $transportError = curl_error($ch); curl_close($ch);
        $data = json_decode((string)$raw,TRUE);
        if ($status<200 || $status>=300 || !is_array($data)) throw new RuntimeException('SSO '.$endpoint.' HTTP '.$status.' '.$transportError.' '.(is_array($data)?(string)($data['error']??''):''));
        return $data;
    }
}
