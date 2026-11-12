<?php
/**
 * Simply Park – site content.
 * Edit the text, images and contact details here; the templates read from this file.
 */

$site = [
    'name'        => 'Simply Park',
    'title'       => 'Simply Park – Parking Solutions',
    'description' => 'Simply Park – Parking Solutions. Parking beyond malls and airports.',
    'logo'        => 'assets/img/SP_logo_PNG.png',
    'favicon'     => 'assets/img/fav.jpg',
    'version'     => 'SP-WEBE-V0.0-260428',
    'year'        => '2026',
];

$hero = [
    'before'    => 'PARKING ',
    'highlight' => 'BEYOND MALLS AND AIRPORTS!',
    'image'     => 'assets/img/Sp-logo-withcircle.png',
    'items'     => [
        [
            'icon'  => 'assets/img/icon1.png',
            'title' => 'HISTORICALLY',
            'text'  => 'Parking Systems were adopted by facilities experiencing a considerable flow of traffic and looking for ways to manage this traffic with possibility of generating revenue.',
            'dark'  => true,  // black title
        ],
        [
            'icon'  => 'assets/img/icon2.png',
            'title' => 'TODAY,',
            'text'  => 'Facility Parking is seeing more challenges than just traffic management and revenue generation.',
            'dark'  => false, // white title
        ],
    ],
];

// "fan" = angled 3D spread, "stack" = flat stack with slight offset
$carousels = [
    [
        'id'      => 'applications',
        'heading' => 'APPLICATIONS',
        'layout'  => 'fan',
        'slides'  => [
            ['Retail-Markets-and-Compounds.jpg', 'Retail Markets and Compounds'],
            ['Residential-and-Office-Facilities.jpg', 'Residential and Office Facilities'],
            ['Open-Commercial-Parking-Areas.jpg', 'Open Commercial Parking Areas'],
            ['Malls-and-Shopping-Plazas.jpg', 'Malls and Shopping Plazas'],
            ['Hotel-Parkings.jpg', 'Hotel Parkings'],
            ['Hospitals-Clinics-and-Medical-Centers.jpg', 'Hospitals, Clinics and Medical Centers'],
            ['Commercial-Facilities-and-Mixed-Used-Facilities.jpg', 'Commercial Facilities and Mixed Used Facilities'],
            ['Sports-Facilities-and-Health-Clubs.jpg', 'Sports Facilities and Health Clubs'],
        ],
    ],
    [
        'id'      => 'trends',
        'heading' => 'TRENDS',
        'layout'  => 'stack',
        'slides'  => [
            ['Cashless-Ticketless.jpg', 'Cashless & Ticketless'],
            ['Digital-Signage.jpg', 'Digital Signage'],
            ['Dynamic-Parking-Charges.jpg', 'Dynamic Parking Charges'],
            ['EV-Charging.jpg', 'EV Charging'],
            ['Find-My-Car.jpg', 'Find My Car'],
            ['Parking-Guidance-Management-under-a-One-Platform.jpg', 'Parking Guidance & Management under a One Platform'],
            ['Parking-Moble-App.jpg', 'Parking Moble App'],
            ['What-if-Number-Plate-does-not-Work.jpg', 'What if Number Plate does not Work'],
        ],
    ],
];

$video = 'assets/video/Final-Clip-SP-1.mp4';

$contact = [
    'address' => 'DAMAC Executive Heights Tower, Al Barsha Heights, Dubai | United Arab Emirates',
    'email'   => 'info@simplypark.com',
    'phone'   => '+971 4263 8186',
];

// Footer "Help" links – set the URLs once the pages exist ('' = plain text).
$help_links = [
    ['Parking kits', ''],
    ['Support Center', ''],
    ['Products', ''],
    ['Solutions', ''],
];

function e($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
