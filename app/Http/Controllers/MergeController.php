<?php

namespace App\Http\Controllers;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;

class MergeController extends Controller
{
    public function index() {

    $new_documents = [];

    $original_documents = ['http://www.africau.edu/images/default/sample.pdf',
                           'https://i0.wp.com/zambianews365.com/wp-content/uploads/2020/02/Bizwell-Mutale.jpeg',
                           'https://www.entnet.org/wp-content/uploads/2021/04/Instructions-for-Adding-Your-Logo-2.pdf',
                           'https://image.freepik.com/free-vector/red-rooster-cock-side-view-abstract_1284-16627.jpg',
                           'https://www.singlestore.com/images/cms/components/section-key-benefit/unlimited-scale_inverted.png'];


    foreach ($original_documents as $document) {

        $extension = strtolower(pathinfo($document, PATHINFO_EXTENSION));

        if ($extension == 'pdf') {

            // save to disk
            $file = file_get_contents($document);
            $file_name = '/documents/'.rand().'.pdf';
            Storage::disk('public')->put($file_name, $file);

            // add to new documents array
            $new_documents[] = $file_name;

        } else if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {

            // convert to pdf
            $dompdf = new Dompdf();

            $options = $dompdf->getOptions(); 
            $options->set(array('isRemoteEnabled' => true));
            $dompdf->setOptions($options);

            $dompdf->loadHtml('<img src="'.$document.'">');

            $dompdf->setPaper('A4', 'landscape');

            $dompdf->render();

            $converted = $dompdf->output();

            $file_name = '/documents/'.rand().'.pdf';

            // save to disk
            Storage::disk('public')->put($file_name, $converted);

            // add to new documents array
            $new_documents[] = $file_name;

        }

    }

    // merge pdf documents
    $pdf = new \Jurosh\PDFMerge\PDFMerger;

    foreach ($new_documents as $document) {

        $path = 'storage'.$document;
  
        $pdf->addPDF($path, 'all', 'horizontal');
    
    }

    $merged_file = 'storage/documents/'.rand().'.pdf';

    $pdf->merge('file', $merged_file);

    return view('welcome',compact('merged_file'));

    }

}
