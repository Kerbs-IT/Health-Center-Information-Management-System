<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Purok Coordinates for Hugo Perez, Trece Martires City
    |--------------------------------------------------------------------------
    |
    | Manually defined coordinates for each purok/subdivision.
    | These are fallback coordinates when geocoding APIs fail.
    |
    | To get coordinates:
    | 1. Go to Google Maps
    | 2. Right-click on the purok/subdivision area
    | 3. Click "What's here?"
    | 4. Copy the coordinates (latitude, longitude)
    |
    */

    'hugo_perez' => [
        // Main barangay center (default fallback)
        'default' => [
            'latitude' => 14.2850,
            'longitude' => 120.8680,
            'name' => 'Hugo Perez'
        ],

        // Individual Puroks/Subdivisions
        'puroks' => [
            'Karlaville Park Homes' => [
                'latitude' => 14.2850,  // UPDATE: Right-click on Google Maps to get actual coordinates
                'longitude' => 120.8680,
                'name' => 'Karlaville Park Homes'
            ],
            'Purok 1' => [
                'latitude' => 14.279043095378052,  // UPDATE: Replace with actual coordinates
                'longitude' => 120.8831416553683,
                'name' => 'Purok 1'
            ],

            'Purok 2' => [
                'latitude' => 14.28094320844904,  // UPDATE: Replace with actual coordinates
                'longitude' => 120.88608001534422,
                'name' => 'Purok 2'
            ],
          
            'Purok 3' => [
                'latitude' => 14.277558864559571,  // UPDATE: Replace with actual coordinates
                'longitude' => 120.88820968930163,
                'name' => 'Purok 3'
            ],
          
            'Purok 4' => [
                'latitude' =>   14.283169505500556,  // UPDATE: Replace with actual coordinates
                'longitude' => 120.89110551628795,
                'name' => 'Purok 4'
            ],
           
            'Purok 5' => [
                'latitude' =>  14.276230410912785,  // UPDATE: Replace with actual coordinates
                'longitude' => 120.89074806203435,
                'name' => 'Purok 5'
            ],
            
            'Purok 6' => [
                'latitude' =>  14.283507413891105,  // UPDATE: Replace with actual coordinates
                'longitude' => 120.89179082069133,
                'name' => 'Purok 6'
            ],
            'Beverly Homes 1' => [
                'latitude' => 14.284517540115818,  // UPDATE: Replace with actual coordinates
                'longitude' =>  120.88893425722082,
                'name' => 'Beverly Homes 1'
            ],
            
            'Beverly Homes 2' => [
                'latitude' =>  14.285940826592235,  // UPDATE: Replace with actual coordinates
                'longitude' => 120.89256214381622,
                'name' => 'Beverly Homes 2'
            ],
            'Green Forbes City' => [
                'latitude' => 14.286985060763875,  // UPDATE: Replace with actual coordinates
                'longitude' => 120.88877977764943,
                'name' => 'Green Forbes City'
            ],
           
            'Gawad Kalinga' => [
                'latitude' => 14.277630739319676,  // UPDATE: Replace with actual coordinates
                'longitude' => 120.8933780427671,
                'name' => 'Gawad Kalinga'
            ],
            
            'Kaia Homes Phase 2' => [
                'latitude' => 14.313209694541815,  // UPDATE: Replace with actual coordinates
                'longitude' => 120.8793377673733,
                'name' => 'Kaia Homes Phase 2'
            ],
           
            'Heneral DOS' => [
                'latitude' => 14.314400464866546,  // UPDATE: Replace with actual coordinates
                'longitude' => 120.88156186737346,
                'name' => 'Heneral DOS'
            ],
            
            'SUGAR LAND' => [
                'latitude' =>  14.282984967345447,  // UPDATE: Replace with actual coordinates
                'longitude' => 120.88519354526487,
                'name' => 'SUGAR LAND'
            ],
           
        ]
    ]
];
