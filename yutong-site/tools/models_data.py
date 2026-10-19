"""Yutong UAE model range -- single source for Home tabs, Models page, Brochures and form dropdowns.

Copy and specs from the client's "YUTONG - Website Copy" document (Oct 2026).
`image` is the asset name in the media-library map (tools/assets.py); filled in as photos are sourced.
"""

CATEGORIES = [
    # (key, plural label used in menus/forms, tab label used on Home, anchor id on the Models page)
    ('coaches', 'Coaches', 'Coach', 'coaches'),
    ('e-coaches', 'E-Coaches', 'E-Coach', 'e-coaches'),
    ('city-buses', 'City Buses', 'City Bus', 'city-buses'),
    ('e-buses', 'E-Buses', 'E-Bus', 'e-buses'),
    ('vans', 'Vans', 'Van', 'vans'),
]

# spec labels per drivetrain, as in the copy document
DIESEL = ('Length (m)', 'Rated Output (kW)', 'Seats', 'Emission')
ELECTRIC = ('Length (m)', 'Battery Capacity (kWh)', 'Seats', 'Max Speed (km/h)')

MODELS = [
    # category, name, tagline, spec values (in DIESEL/ELECTRIC order), electric?
    ('coaches', 'C9', 'Compact in size, built for comfortable and dependable passenger travel.', ('9', '199', '39', 'Euro V'), False),
    ('coaches', 'C11', 'Built for comfortable, dependable coach travel across everyday and longer journeys.', ('11', '228', '47', 'Euro V'), False),
    ('coaches', 'C12 Pro', 'High-capacity coach travel with comfort designed for longer journeys.', ('12', '243', '53', 'Euro V'), False),
    ('coaches', 'D7', 'Versatile passenger transport with practical capacity for everyday journeys.', ('7', '125', '29 / 21 with Rear Luggage Compartment', 'Euro V'), False),

    ('e-coaches', 'C9E', 'Compact electric coach travel with comfort and efficiency at its core.', ('9', '255', '39', '100'), True),
    ('e-coaches', 'C12E', 'Electric coach travel with generous capacity and comfort for longer journeys.', ('12', '350', '51', '100'), True),
    ('e-coaches', 'D8E', 'Electric coach mobility with practical capacity and comfort for everyday passenger travel.', ('8', '176.04', '30', '80'), True),
    ('e-coaches', 'E11', 'Designed to move more people efficiently across busy urban routes.', ('11', '352', '33', '80'), True),
    ('e-coaches', 'IC12E', 'Electric intercity mobility with a premium focus on comfort and efficiency.', ('12', '465.99', '46', '100'), True),
    ('e-coaches', 'T12E', 'Premium electric coach travel designed for smooth, refined long-distance journeys.', ('12', '465.99', '51', '100'), True),

    ('city-buses', 'ZK6126HG', 'High-capacity city transport designed for efficient, reliable daily operation.', ('12', '243', '42', 'Euro 6'), False),
    ('city-buses', 'ZK6810HG', 'Compact city mobility designed for efficient movement across busy daily routes.', ('8', '149', '20', 'Euro 6'), False),
    ('city-buses', 'ZK6890HGQ', 'Versatile city transport designed for reliable performance across busy urban routes.', ('9', '185', '24', 'Euro 6'), False),

    ('e-buses', 'U18', 'Large-scale passenger movement, electrified.', ('18', '621', '47', '70'), True),
    ('e-buses', 'U12', 'Electric city travel with the capacity and flexibility for everyday routes.', ('12', '465/528', '44', '80'), True),
    ('e-buses', 'E7S', 'Compact electric mobility for smarter urban journeys.', ('7', '176.04', '16', '70'), True),
    ('e-buses', 'E9', 'Modern electric mobility with space, comfort, and efficiency.', ('9', '255.02', '26', '80'), True),
    ('e-buses', 'E12', 'Advanced electric transport with the capacity to move more passengers.', ('12', '399.92', '39', '80'), True),

    ('vans', 'V6', 'A versatile van solution for corporate, hospitality, and shuttle transport.', ('6', '120', '14', 'Euro V'), False),
    ('vans', 'V6E', 'A versatile van solution for corporate, hospitality, and shuttle transport.', ('6', '104.32', '14', '120'), True),
]


def slug(name):
    return name.lower().replace(' ', '-')


def image_name(name):
    """Media-library asset name for a model's side-view photo."""
    return 'model-' + slug(name)


def models(category=None):
    out = []
    for cat, name, tag, vals, electric in MODELS:
        if category and cat != category:
            continue
        labels = ELECTRIC if electric else DIESEL
        out.append({'category': cat, 'name': name, 'slug': slug(name), 'tagline': tag, 'electric': electric,
                    'specs': list(zip(labels, vals)), 'image': image_name(name)})
    return out


def label(category):
    return next(c[1] for c in CATEGORIES if c[0] == category)
