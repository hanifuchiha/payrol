<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$__pdf_error_reporting = error_reporting();
error_reporting($__pdf_error_reporting & ~E_DEPRECATED & ~E_WARNING & ~E_NOTICE);
$__pdf_autoload_candidates = array(
	FCPATH . 'library/dompdf/autoload.inc.php',
	dirname(APPPATH) . '/library/dompdf/autoload.inc.php',
	dirname(FCPATH) . '/library/dompdf/autoload.inc.php',
);
$__pdf_autoload = '';
foreach ($__pdf_autoload_candidates as $__pdf_candidate)
{
	if (file_exists($__pdf_candidate))
	{
		$__pdf_autoload = $__pdf_candidate;
		break;
	}
}
if ($__pdf_autoload === '')
{
	show_error('Dompdf library tidak ditemukan. Upload folder library/dompdf ke proyek payrol.');
}
require_once $__pdf_autoload;
error_reporting($__pdf_error_reporting);

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf {

	public function create($html, $filename = 'document.pdf', $paper = 'A4', $orientation = 'portrait', $stream = TRUE)
	{
		$previous_level = error_reporting();
		error_reporting($previous_level & ~E_DEPRECATED & ~E_WARNING & ~E_NOTICE);

		$options = new Options();
		$options->set('isRemoteEnabled', TRUE);

		$dompdf = new Dompdf($options);
		$dompdf->loadHtml($html);
		$dompdf->setPaper($paper, $orientation);
		$dompdf->render();

		if ($stream)
		{
			$dompdf->stream($filename, array('Attachment' => TRUE));
			error_reporting($previous_level);
			return;
		}

		$output = $dompdf->output();
		error_reporting($previous_level);

		return $output;
	}
}
