<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class RemoveBackgroundController extends Controller
{

    public function imageStore(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpg,png,jpeg',
        ]);

        if ($fileName = $request->file('image')) {
            $inputImage = public_path('convert/inputs/');
//            $maskImage = public_path('convert/masks/');
            $resultDir = public_path('convert/results/');
        }

        $mainPy = base_path('Image-Background-Remover-Python/__init__.py');
        $process = new Process(['python3', $mainPy, $fileName, $inputImage, $resultDir]);

        $process->run();

        // error handling
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        //output from python script
        $output_data = $process->getOutput();
        $str = (string)$output_data;
        $trimData = rtrim($str, "\n");

        //image path
        $imageFilePath = public_path() . '/convert/results/' . $trimData;

        $fileObject = createFileObject($imageFilePath);

        $data = [
            'url' => asset('convert/results/' . $trimData),
            'filename' => $trimData,
            'image' => $fileObject,
        ];

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => 'Image Successfully Converted.',
            'data' => $data,
        ], 201);
    }
}
