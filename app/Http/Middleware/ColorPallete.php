<?php

namespace App\Http\Middleware;

use App\Models\color_pallete;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ColorPallete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    { // Let the request continue and get the response
        $response = $next($request);

        // Only inject for HTML responses (not JSON, CSS, images, etc.)
        if (
            $response->headers->get('Content-Type') &&
            str_contains($response->headers->get('Content-Type'), 'text/html')
        ) {

            // Get colors from database/cache
            cache()->forget('color_palette');
            $colorPalette = cache()->remember('color_palette', 3600, function () {

              
                $palette = color_pallete::where('id',1)-> first();
                return [
                    'primary_color' => $palette->primaryColor ?? '#FFFFFF',
                    'secondary_color' => $palette->secondaryColor ?? '#065A24',
                    'tertiary_color' => $palette->tertiaryColor ?? '#2E8B57',
                ];
            });

            // Build the CSS string
            $cssVars = "<style>:root{" .
                "--primaryColor:" . ($colorPalette['primary_color'] ?? '#FFFFFF') . ";" .
                "--secondaryColor:" . ($colorPalette['secondary_color'] ?? '#065A24') . ";" .
                "--tertiaryColor:" . ($colorPalette['tertiary_color'] ?? '#2E8B57') . ";" .
                "}</style>";

            // Get the HTML content that Laravel generated
            $content = $response->getContent();

            // Find </head> and inject our CSS before it
            $content = str_replace('</head>', $cssVars . '</head>', $content);

            // Put the modified HTML back into the response
            $response->setContent($content);
        }

        // Send the response to the browser
        return $response;
    }
}
