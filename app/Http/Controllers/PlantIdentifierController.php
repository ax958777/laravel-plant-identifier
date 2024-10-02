<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;

class PlantIdentifierController extends Controller
{
    public function index()
    {
        return Inertia::render('PlantIdentifier');
    }

    public function identify(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        $image = $request->file('image');
        $imageData = base64_encode(file_get_contents($image->path()));

        // Call Google Gemini API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            //'Authorization' => 'Bearer ' . config('services.gemini.api_key'),
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=AIzaSyApcAfgd_TCyQPNk2jK1_7NDOzfd4cNhxE', [
            'contents' => [
                'parts' => [
                    [
                        'text' => 'Identify this plant and provide important information about it:',
                    ],
                    [
                        'inline_data' => [
                            'mime_type' => 'image/jpeg',
                            'data' => $imageData,
                        ],
                    ],
                ],
            ],
        ]);

        $result = $response->json();

        //dump($result);

        // Process the response and extract relevant information
        $plantInfo = $this->processGeminiResponse($result);

        //return response()->json($plantInfo);

        return Inertia::render('PlantIdentifier', [
            'plantInfo' => $plantInfo,
        ]);
    }

    private function processGeminiResponse($result)
    {
        // Extract relevant information from the Gemini API response
        // This is a simplified example; you may need to adjust based on the actual response structure
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        // Parse the text to extract plant name and information
        // This is a basic example; you might want to use more sophisticated parsing
        $lines = explode("\n", $text);
        $plantName = trim(str_replace('Plant Name:', '', $lines[0] ?? ''));
        $information = implode("\n", array_slice($lines, 1));

        return [
            'name' => $plantName,
            'information' => $information,
        ];
    }
}