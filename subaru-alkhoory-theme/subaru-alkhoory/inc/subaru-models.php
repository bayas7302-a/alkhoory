<?php
/**
 * Subaru model data: the single source of truth for the image pack in
 * wp-content/uploads/subaru-assets/ (colours, grades, interiors, galleries, logos).
 *
 * All paths are relative to the asset base (see subaru_asset_url()). File names match the pack exactly.
 * Grade names follow the site copy; where the pack only has a neighbouring grade, the nearest
 * match is used and marked with 'note' (see SUBARU-SITE.md).
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(

	/*
	 * Paint colours. Key = folder/file key in the pack. Hex = swatch colour (checked against the
	 * colour-picker images; Cashmere Gold Opal adjusted to match the photographed paint).
	 */
	'colours'    => array(
		'Crystal_White_Pearl'       => array( 'name' => 'Crystal White Pearl', 'hex' => '#F1F1EC' ),
		'Ceramic_White'             => array( 'name' => 'Ceramic White', 'hex' => '#ECECE8' ),
		'Ice_Silver_Metallic'       => array( 'name' => 'Ice Silver Metallic', 'hex' => '#B9BDC0' ),
		'Mag_Grey_Metallic'         => array( 'name' => 'Magnetite Grey Metallic', 'hex' => '#4B4E52' ),
		'Crystal_Black_Silica'      => array( 'name' => 'Crystal Black Silica', 'hex' => '#121315' ),
		'Sapphire_Blue_Pearl'       => array( 'name' => 'Sapphire Blue Pearl', 'hex' => '#1C2B57' ),
		'WR_Blue_Pearl'             => array( 'name' => 'WR Blue Pearl', 'hex' => '#1E4BAA' ),
		'Ignition_Red'              => array( 'name' => 'Ignition Red', 'hex' => '#C8102E' ),
		'Crimson_Red_Pearl'         => array( 'name' => 'Crimson Red Pearl', 'hex' => '#7B1521' ),
		'Galaxy_Purple_Pearl'       => array( 'name' => 'Galaxy Purple Pearl', 'hex' => '#3C2F5C' ),
		'Autumn_Green_Metallic'     => array( 'name' => 'Autumn Green Metallic', 'hex' => '#5D6249' ),
		'Brilliant_Bronze_Metallic' => array( 'name' => 'Brilliant Bronze Metallic', 'hex' => '#8C6F59' ),
		'Cashmere_Gold_Opal'        => array( 'name' => 'Cashmere Gold Opal', 'hex' => '#B7AA99' ),
		'Horizon_Blue_Pearl'        => array( 'name' => 'Horizon Blue Pearl', 'hex' => '#5F809D' ),
		'River_Rock_Pearl'          => array( 'name' => 'River Rock Pearl', 'hex' => '#8F8C86' ),
		'Sand_Dune_Pearl'           => array( 'name' => 'Sand Dune Pearl', 'hex' => '#C9BDA4' ),
		'Citron_Yellow_Pearl'       => array( 'name' => 'Citron Yellow Pearl', 'hex' => '#C9D33D' ),
	),

	// Interior trim swatch colours ('view' = a camera view such as the dashboard, shown with an icon).
	'trims'      => array(
		'black'    => '#1a1a1a',
		'brown'    => '#6b3f2a',
		'platinum' => '#c9c6bf',
	),

	// Technology logos: %s = original-colour | white | dark.
	'tech_logos' => array(
		'awd'         => array( 'name' => 'Symmetrical All-Wheel Drive', 'file' => '01_Logos/Technology/Symmetrical-AWD_logo_%s.png' ),
		'boxer'       => array( 'name' => 'SUBARU BOXER', 'file' => '01_Logos/Technology/BOXER_logo_%s.png' ),
		'eyesight'    => array( 'name' => 'EyeSight Driver Assist Technology', 'file' => '01_Logos/Technology/EyeSight_v2_logo_%s.png' ),
		'eyesight-v1' => array( 'name' => 'EyeSight Driver Assist Technology', 'file' => '01_Logos/Technology/EyeSight_v1_logo_%s.png' ),
	),

	'models'     => array(

		'forester'  => array(
			'slug'           => 'forester',
			'name'           => 'Forester',
			'logo'           => '01_Logos/Model_names/Forester_logo_%s.png',
			'hero'           => '', // No lifestyle photo in the pack: the page keeps its current hero.
			'home_image'     => '07_Colour_Picker/Forester/Forester_Sapphire_Blue_Pearl_front34.jpg',
			'picker'         => '07_Colour_Picker/Forester/Forester_%s_front34.jpg',
			'spin_dir'       => '06_360_Spins/Forester',
			'spin_frames'    => 12,
			'default_colour' => 'Sapphire_Blue_Pearl',
			'colours'        => array( 'Sapphire_Blue_Pearl', 'Crystal_White_Pearl', 'Ice_Silver_Metallic', 'Mag_Grey_Metallic', 'Crystal_Black_Silica', 'Crimson_Red_Pearl', 'Autumn_Green_Metallic', 'Brilliant_Bronze_Metallic', 'Cashmere_Gold_Opal', 'Horizon_Blue_Pearl', 'River_Rock_Pearl' ),
			// Trim order requested by the client (9 Nov): 2.5i, 2.5i Sport, 2.5i-S EyeSight.
			'grades'         => array(
				// No Forester grade cut-outs exist: the cards use colour-picker images in different colours.
				array(
					'name'     => 'Forester 2.5i EyeSight',
					'image'    => '07_Colour_Picker/Forester/Forester_Crystal_White_Pearl_front34.jpg',
					'colour'   => 'Crystal_White_Pearl',
					'interior' => '2.5i',
				),
				array(
					'name'     => 'Forester 2.5i-Sport EyeSight',
					'image'    => '07_Colour_Picker/Forester/Forester_Mag_Grey_Metallic_front34.jpg',
					'colour'   => 'Mag_Grey_Metallic',
					'interior' => '2.5i-Sport',
				),
				array(
					'name'     => 'Forester 2.5i-S EyeSight',
					'image'    => '07_Colour_Picker/Forester/Forester_Sapphire_Blue_Pearl_front34.jpg',
					'colour'   => 'Sapphire_Blue_Pearl',
					'interior' => '2.5i-S',
				),
			),
			// Dashboards are the client's left-hand-drive photos (Media Library); the pack ones are right-hand drive.
			// Left-hand-drive interior (Subaru SMP 'Interior 360° View Data'): 'still' photo shown first, cube faces (front, right, back, left, up, down) for the 360° view, 'yaw' = starting direction.
			'interior360'    => array(
				array( 'label' => 'Cabin', 'still' => 1094, 'yaw' => 0, 'faces' => array( 1045, 1046, 1047, 1048, 1049, 1050 ) ),
			),
			// Interior photos (left-hand drive), each linked to a 360° scene and starting angle.
			// Official Subaru left-hand-drive (E3/C0 spec) interior photos from the SMP portal; the 360° view is separate.
			'interior_views' => array(
				array( 'label' => 'Dashboard', 'still' => 1116 ),
				array( 'label' => 'Front cabin', 'still' => 1117 ),
				array( 'label' => 'Steering wheel', 'still' => 1118 ),
				array( 'label' => 'Multimedia display', 'still' => 1119 ),
				array( 'label' => 'Instrument cluster', 'still' => 1120 ),
				array( 'label' => 'Rear seats', 'still' => 1121 ),
				array( 'label' => 'Rear seats folded', 'still' => 1122 ),
				array( 'label' => 'Panoramic sunroof', 'still' => 1123 ),
			),
			'interiors'      => array(
				'2.5i'       => array(
					'label' => '2.5i EyeSight',
					'views' => array(
						array( 'label' => 'Cabin', 'trim' => 'view', 'id' => 961 ),
						array( 'label' => 'Black', 'trim' => 'black', 'file' => '05_Interiors/Forester/Forester_2.5i-EyeSight_interior_seats_black.jpg' ),
						array( 'label' => 'Platinum', 'trim' => 'platinum', 'file' => '05_Interiors/Forester/Forester_2.5i-EyeSight_interior_seats_platinum.jpg' ),
					),
				),
				'2.5i-Sport' => array(
					'label' => '2.5i-Sport EyeSight',
					'views' => array(
						array( 'label' => 'Dashboard', 'trim' => 'view', 'id' => 960 ),
						array( 'label' => 'Seats', 'trim' => 'black', 'file' => '05_Interiors/Forester/Forester_2.5i-Sport-EyeSight_interior_seats.jpg' ),
					),
				),
				'2.5i-S'     => array(
					'label' => '2.5i-S EyeSight',
					'views' => array(
						array( 'label' => 'Black leather', 'trim' => 'black', 'file' => '05_Interiors/Forester/Forester_2.5i-S-EyeSight_interior_seats_black.jpg' ),
						array( 'label' => 'Brown leather', 'trim' => 'brown', 'file' => '05_Interiors/Forester/Forester_2.5i-S-EyeSight_interior_seats_brown.jpg' ),
					),
				),
			),
			// Approved UAE photoshoot images (Media Library), shown on the Media Gallery page.
			'gallery_media'  => array( 1155, 1156, 1151, 1175, 1173, 1152, 1154, 1174, 1153, 988, 994, 995, 996, 998, 999 ),
			'gallery'        => array(), // None in the pack: the page keeps its current gallery.
		),

		'crosstrek' => array(
			'slug'           => 'crosstrek',
			'name'           => 'Crosstrek',
			'logo'           => '01_Logos/Model_names/Crosstrek_logo_%s.png',
			'hero'           => '04_Media_Gallery/Crosstrek_exterior_desert_blue.jpg',
			'hero_alt'       => 'Subaru Crosstrek in blue driving through the desert',
			'home_image'     => '02_Car_Cutouts/Crosstrek/Crosstrek_2.0i-S-EyeSight-eBOXER_SandDunePearl_front34.jpg',
			'picker'         => '07_Colour_Picker/Crosstrek/Crosstrek_%s_front34.jpg',
			'spin_dir'       => '06_360_Spins/Crosstrek',
			'spin_frames'    => 12,
			'default_colour' => 'Sand_Dune_Pearl',
			'colours'        => array( 'Sand_Dune_Pearl', 'Citron_Yellow_Pearl', 'Crystal_White_Pearl', 'Ice_Silver_Metallic', 'Mag_Grey_Metallic', 'Crystal_Black_Silica', 'Sapphire_Blue_Pearl' ),
			'grades'         => array(
				array(
					'name'     => 'Crosstrek 2.0i-S EyeSight',
					'image'    => '02_Car_Cutouts/Crosstrek/Crosstrek_2.0i-S-EyeSight-eBOXER_SandDunePearl_front34.jpg',
					'colour'   => 'Sand_Dune_Pearl',
					'interior' => '2.0i-S',
				),
				array(
					'name'     => 'Crosstrek 2.0i-L EyeSight',
					'image'    => '02_Car_Cutouts/Crosstrek/Crosstrek_2.5i-L-EyeSight_CitronYellowPearl_front34.jpg',
					'colour'   => 'Citron_Yellow_Pearl',
					'interior' => '2.0i-L',
					'note'     => 'Pack has no 2.0i-L: uses the 2.5i-L EyeSight cut-out and interior.',
				),
			),
			// Left-hand-drive interior (Subaru SMP 'Interior 360° View Data'): 'still' photo shown first, cube faces (front, right, back, left, up, down) for the 360° view, 'yaw' = starting direction.
			'interior360'    => array(
				array( 'label' => 'Cabin', 'still' => 1096, 'yaw' => 0, 'faces' => array( 1057, 1058, 1059, 1060, 1061, 1062 ) ),
			),
			// Interior photos (left-hand drive), each linked to a 360° scene and starting angle.
			// Official Subaru left-hand-drive (E3/C0 spec) interior photos from the SMP portal; the 360° view is separate.
			'interior_views' => array(
				array( 'label' => 'Dashboard', 'still' => 1124 ),
				array( 'label' => 'Front cabin', 'still' => 1125 ),
				array( 'label' => 'Steering wheel', 'still' => 1126 ),
				array( 'label' => 'Multimedia display', 'still' => 1127 ),
				array( 'label' => 'Instrument cluster', 'still' => 1128 ),
				array( 'label' => 'Front seats', 'still' => 1129 ),
				array( 'label' => 'Rear seats', 'still' => 1130 ),
				array( 'label' => 'Rear seats folded', 'still' => 1131 ),
				array( 'label' => 'Sunroof', 'still' => 1132 ),
			),
			'interiors'      => array(
				'2.0i-S' => array(
					'label' => '2.0i-S EyeSight',
					'views' => array(
						array( 'label' => 'Dashboard', 'trim' => 'view', 'file' => '05_Interiors/Crosstrek/Crosstrek_2.0i-S-eBOXER_interior_dashboard.jpg' ),
					),
				),
				'2.0i-L' => array(
					'label' => '2.0i-L EyeSight',
					'views' => array(
						array( 'label' => 'Dashboard', 'trim' => 'view', 'file' => '05_Interiors/Crosstrek/Crosstrek_2.5i-L-EyeSight_interior_dashboard.jpg' ),
					),
				),
			),
			// Media Library photos already used on the model page (shown on the Media Gallery page).
			'gallery_media'  => array( 327, 232, 304, 331, 295, 291, 296, 299, 301, 336, 329, 1042, 1043 ),
			'gallery'        => array(
				array( '04_Media_Gallery/Crosstrek_exterior_coastroad_blue.jpg', 'Subaru Crosstrek in blue on a coastal road' ),
				array( '04_Media_Gallery/Crosstrek_exterior_desert_blue.jpg', 'Subaru Crosstrek in blue driving through the desert' ),
				array( '04_Media_Gallery/Crosstrek_exterior_offroad_sanddune.jpg', 'Subaru Crosstrek in Sand Dune Pearl on an off-road trail' ),
				array( '04_Media_Gallery/Crosstrek_exterior_rocks_blue.jpg', 'Subaru Crosstrek in blue parked among rocks' ),
				array( '04_Media_Gallery/Crosstrek_exterior_snow_red.jpg', 'Subaru Crosstrek in red on a snowy road' ),
				array( '04_Media_Gallery/Crosstrek_lifestyle_cycling_silver.jpg', 'Subaru Crosstrek in silver with bikes on a cycling trip' ),
				array( '04_Media_Gallery/Crosstrek_lifestyle_mountains_red.jpg', 'Subaru Crosstrek in red in the mountains' ),
			),
		),

		'wrx'       => array(
			'slug'           => 'wrx',
			'name'           => 'WRX',
			'logo'           => '01_Logos/Model_names/WRX_logo_%s.png',
			'hero'           => '04_Media_Gallery/WRX_exterior_road_blue.jpg',
			'hero_alt'       => 'Subaru WRX in blue on the road',
			'home_image'     => '02_Car_Cutouts/WRX/WRX_tS-EyeSight-MT_WRBluePearl_front34.jpg',
			'picker'         => '07_Colour_Picker/WRX/WRX_%s_front34.jpg',
			'spin_dir'       => '06_360_Spins/WRX',
			'spin_frames'    => 18,
			'default_colour' => 'WR_Blue_Pearl',
			// Core palette only (client brief, 9 Nov): blue, white and silver.
			'colours'        => array( 'WR_Blue_Pearl', 'Ceramic_White', 'Ice_Silver_Metallic' ),
			'grades'         => array(
				array(
					'name'     => 'WRX tS EyeSight',
					'image'    => '02_Car_Cutouts/WRX/WRX_tS-EyeSight-CVT_CeramicWhite_front34.jpg',
					'colour'   => 'Ceramic_White',
					'interior' => 'ts',
				),
			),
			// Left-hand-drive interior (Subaru SMP 'Interior 360° View Data'): 'still' photo shown first, cube faces (front, right, back, left, up, down) for the 360° view, 'yaw' = starting direction.
			'interior360'    => array(
				array( 'label' => 'Cabin', 'still' => 1098, 'yaw' => 0, 'faces' => array( 1069, 1070, 1071, 1072, 1073, 1074 ) ),
			),
			// Interior photos (left-hand drive), each linked to a 360° scene and starting angle.
			// Official Subaru left-hand-drive (E3/C0 spec) interior photos from the SMP portal; the 360° view is separate.
			'interior_views' => array(
				array( 'label' => 'Cabin (automatic)', 'still' => 1133 ),
				array( 'label' => 'Cabin (manual)', 'still' => 1134 ),
				array( 'label' => 'Steering wheel', 'still' => 1135 ),
				array( 'label' => 'Multimedia display', 'still' => 1136 ),
				array( 'label' => 'Instrument cluster', 'still' => 1137 ),
				array( 'label' => 'Recaro seat', 'still' => 1138 ),
				array( 'label' => 'Automatic selector', 'still' => 1139 ),
				array( 'label' => '6-speed manual', 'still' => 1140 ),
			),
			'interiors'      => array(
				'ts'   => array(
					'label' => 'tS EyeSight',
					'views' => array(
						array( 'label' => 'Recaro sports seats', 'trim' => 'black', 'file' => '05_Interiors/WRX/WRX_tS_interior_recaro.jpg' ),
					),
				),
			),
			// Approved UAE photoshoot images (Media Library); blue WRX tS only.
			'gallery_media'  => array( 1164, 1163, 1158, 1159, 1157, 1161, 989, 1006, 1165, 1162, 1008, 1160, 990, 1009, 1011 ),
			'gallery'        => array(), // Pack photos replaced by the approved photoshoot images above.
		),

		'brz'       => array(
			'slug'           => 'brz',
			'name'           => 'BRZ',
			// The BRZ file holds two lock-ups (horizontal on top, stacked below); CSS shows the horizontal one.
			'logo'           => '01_Logos/Model_names/BRZ_logo_%s.png',
			'logo_crop'      => 'top',
			'hero'           => '04_Media_Gallery/BRZ_photo_M0150_blue.jpg',
			'hero_alt'       => 'Subaru BRZ in blue',
			'home_image'     => '02_Car_Cutouts/BRZ/BRZ_MT_WRBluePearl_side34.jpg',
			'picker'         => '07_Colour_Picker/BRZ/BRZ_%s_front34.jpg',
			'spin_dir'       => '06_360_Spins/BRZ',
			'spin_frames'    => 36,
			'default_colour' => 'WR_Blue_Pearl',
			'colours'        => array( 'WR_Blue_Pearl', 'Ignition_Red', 'Crystal_White_Pearl', 'Ice_Silver_Metallic', 'Mag_Grey_Metallic', 'Crystal_Black_Silica', 'Sapphire_Blue_Pearl' ),
			'grades'         => array(
				array(
					'name'     => 'Subaru BRZ Automatic',
					'image'    => '02_Car_Cutouts/BRZ/BRZ_EyeSight-AT_CrystalBlackSilica_front34.jpg',
					'colour'   => 'Crystal_Black_Silica',
					'interior' => 'brz',
				),
				// Manual keeps the silver cut-out already on the site.
				array(
					'name'     => 'Subaru BRZ Manual',
					'image'    => '',
					'interior' => 'brz',
				),
			),
			// Left-hand-drive interior (Subaru SMP 'Interior 360° View Data'): 'still' photo shown first, cube faces (front, right, back, left, up, down) for the 360° view, 'yaw' = starting direction.
			'interior360'    => array(
				array( 'label' => 'Manual', 'still' => 1100, 'yaw' => 0, 'faces' => array( 1081, 1082, 1083, 1084, 1085, 1086 ) ),
			),
			// Interior photos (left-hand drive), each linked to a 360° scene and starting angle.
			// Official Subaru left-hand-drive (E3/C0 spec) interior photos from the SMP portal; the 360° view is separate.
			'interior_views' => array(
				array( 'label' => 'Dashboard', 'still' => 1141 ),
				array( 'label' => 'Front cabin', 'still' => 1142 ),
				array( 'label' => 'Sports seats', 'still' => 1143 ),
				array( 'label' => 'Digital cluster', 'still' => 1144 ),
				array( 'label' => 'Paddle shifters', 'still' => 1145 ),
				array( 'label' => '6-speed manual', 'still' => 1146 ),
				array( 'label' => 'Climate control', 'still' => 1147 ),
				array( 'label' => 'Aluminium pedals', 'still' => 1148 ),
			),
			'interiors'      => array(
				'brz' => array(
					'label' => 'BRZ',
					'views' => array(
						array( 'label' => 'Dashboard', 'trim' => 'view', 'file' => '05_Interiors/BRZ/BRZ_interior_dashboard.jpg' ),
						array( 'label' => 'Seats', 'trim' => 'black', 'file' => '05_Interiors/BRZ/BRZ_interior_seats.jpg' ),
					),
				),
			),
			// Approved UAE photoshoot images (Media Library), shown on the model page and the Media Gallery page.
			'gallery_media'  => array( 1166, 1169, 1167, 1170, 1168, 1171, 980, 981, 982, 1172, 984, 985 ),
			'gallery'        => array(), // Pack photos replaced by the approved photoshoot images above.
		),
	),
);
