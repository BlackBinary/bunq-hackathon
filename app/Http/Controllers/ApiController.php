<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GoogleCloudVision\GoogleCloudVision;
use GoogleCloudVision\Request\AnnotateImageRequest;
use Aws\S3\S3Client;

class ApiController extends Controller
{
    protected $S3Client;

    public function __construct()
    {
        $this->S3Client = new S3Client([
            'version' => 'latest',
            'region' => env('S3_REGION'),
            'endpoint' => env('S3_ENDPOINT'),
            'credentials' => [
                'key' => env('S3_ACCESS_KEY'),
                'secret' => env('S3_ACCESS_SECRET')
            ],
        ]);
    }

    public function getJSON($hash)
    {
        $result = $this->getS3($this->createTextKey($hash));

        return $result['Body'];
    }

    public function createReceipt(Request $request)
    {
        $hash = $this->generateHash();

        $image = $request->receipt;

        $base64Image = base64_encode(file_get_contents($image));

        $request = new AnnotateImageRequest();
        $request->setImage($base64Image);
        $request->setFeature("DOCUMENT_TEXT_DETECTION");
        $gcvRequest = new GoogleCloudVision([$request], env('GOOGLE_CLOUD_KEY'));
        //send annotation request
        $response = $gcvRequest->annotate();


        $this->storeS3File($this->createImageKey($hash), $image, 'image/png');
        $this->storeS3Object($this->createTextKey($hash), json_encode($response), 'application/json');

        return response()->json([
            'hash' => $hash
        ]);
    }

    public function getS3($key)
    {
        $result = $this->S3Client->getObject([
            'Bucket' => env('BUCKET_NAME'),
            'Key' => $key,
        ]);

        return $result;
    }

    public function storeS3Object($key, $body, $mime)
    {
        $result = $this->S3Client->putObject([
            'Bucket' => env('BUCKET_NAME'),
            'Key' => $key,
            'Body' => $body,
            'ContentType' => $mime,
            'ACL' => 'public-read'
        ]);

        return $result;
    }

    public function storeS3File($key, $file, $mime)
    {
        $result = $this->S3Client->putObject([
            'Bucket' => env('BUCKET_NAME'),
            'Key' => $key,
            'SourceFile' => $file,
            'ContentType' => $mime,
            'ACL' => 'public-read'
        ]);

        return $result;
    }

    public function createImageKey($hash)
    {
        return $hash . '.png';
    }

    public function createTextKey($hash)
    {
        return $hash . '.json';
    }

    public function generateHash()
    {
        return bin2hex(random_bytes(16));
    }

}
