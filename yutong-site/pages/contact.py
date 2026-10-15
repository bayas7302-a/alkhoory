"""Contact page (Figma frame 1:1231 'Contact') -> WP page 11."""
import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'tools'))
from parts import *

POST = 11
SLUG = 'contact'

# Copy sources: en.yutong.com /contact/contact/, /service/, /about/about-yutong/; contact details = UAE distributor
HERO_TEXT = ('Contact Al Khoory Automobiles, the official Yutong distributor in the UAE, for vehicle specifications, fleet requirements, '
             'customization, spare parts, training and customer support. Send your enquiry online, call us or visit our Dubai showroom.')
YT_SOCIAL = [('Facebook', 'https://www.facebook.com/YutongGlobal/'), ('LinkedIn', 'https://www.linkedin.com/company/9181418/'),
             ('Instagram', 'https://www.instagram.com/yutong.global/'), ('YouTube', 'https://www.youtube.com/channel/UCIxtne2jWoPbjtC6SidsvSw'),
             ('X', 'https://twitter.com/YutongGlobal')]
MAP_URL = 'https://maps.google.com/?q=Al+Khoory+Automobiles+Al+Quoz+Industrial+Area+3+Dubai'


def kicker(cid, text, color='#0160b4'):
    """This frame's eyebrow is a touch larger than the shared one (13px, 24px rule)."""
    return flex(cid, 'width: auto; padding: 0; gap: 10px; align-items: center;', [
        div(cid + ' Rule', 'width: 24px; min-width: 24px; height: 2px; padding: 0; background-color: %s;' % color),
        p(cid + ' Text', text, FB + 'font-size: 13px; font-weight: 600; letter-spacing: 1.56px; text-transform: uppercase; color: %s; margin: 0;' % color).c('yt-nowrap'),
    ])


def help_section():
    topics = [('contact-ic-bus-blue', 'Vehicle & fleet information'), ('contact-ic-cog', 'Configuration & customization'),
              ('contact-ic-wrench', 'Parts & service support'), ('contact-ic-grad', 'Training'), ('contact-ic-brief', 'Other business inquiries')]
    rows = []
    for i, (ic, t) in enumerate(topics):
        on = i == 0
        n = 'Help Topic %d' % (i + 1)
        rows.append(flex(n, 'padding: 18px 22px; gap: 16px; align-items: center; border-radius: 18px; %s %s @media(--mobile) { padding: 14px 16px; gap: 12px; }' % (
            'background-color: #0160b4;' if on else 'background-color: #f2f6fb; &:hover { background-color: #e3effa; }', EASE), [
            flex(n + ' Icon', 'width: 44px; min-width: 44px; height: 44px; padding: 0; border-radius: 22px; background-color: #ffffff; justify-content: center; align-items: center;', [img(n + ' Glyph', ic, 'width: 19px;')]),
            p(n + ' Label', t, FD + 'flex: 1 1 0; font-size: 17px; font-weight: 600; color: %s; margin: 0; @media(--mobile) { font-size: 15px; }' % ('#ffffff' if on else '#00050b')),
            img(n + ' Arrow', 'contact-arrow-white-18' if on else 'contact-arrow-blue-18', 'width: 18px; min-width: 18px;'),
        ], href=URL['quote']))
    return flex('How Can We Help', 'padding: 110px 120px; gap: 64px; align-items: flex-start; background-color: #ffffff; @media(--tablet) { flex-direction: column; padding: 80px 40px; gap: 44px; } @media(--mobile) { padding: 64px 20px; gap: 32px; }', [
        flex('Help Text', 'width: 520px; max-width: 100%; flex: 0 0 auto; padding: 0; flex-direction: column; gap: 18px; @media(--tablet) { width: 100%; }', [
            flex('Help Head', 'padding: 0; flex-direction: column; gap: 14px;', [
                kicker('Help Eyebrow', 'Get in Touch'),
                title('Help Title', 'How Can We ' + hl('Help?'), 'font-size: 42px; font-weight: 700; line-height: 52px; letter-spacing: 0; color: #00050b; @media(--mobile) { line-height: 38px; }'),
            ]),
            body('Help Body', 'Choose the most relevant inquiry type: vehicle and fleet information, configuration and customization, parts and service support, training, or other business inquiries. Together with Yutong we offer a range of services, including extended warranty products, customized buses, sourcing Yutong bus parts, and support services such as service station construction, charging station construction, vehicle networking services, and training services.', 'line-height: 26px;', '#747474'),
            p('Help Team', "Backed by Yutong's dedicated team of 198 service professionals and 68 international experts, our UAE team is your partner in creating value and supporting your fleet's success. Find our showroom and contact details below.",
              FD + 'font-size: 16px; font-weight: 500; line-height: 26px; color: #003376; margin: 0;'),
        ]),
        flex('Help Topics', 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 14px; @media(--tablet) { width: 100%; flex: 0 0 auto; }', rows),
    ], tag='section')


def info_card(n, icon, label, value, href=None, dark=False):
    cid = 'Contact Card ' + n
    return flex(cid, 'padding: 20px; flex: 1 1 0; gap: 14px; align-items: center; border-radius: 18px; %s @media(--mobile) { flex: 0 0 auto; padding: 16px; }' % (
        'background-color: #072b52;' if dark else 'background-color: #ffffff; box-shadow: 0 10px 14px rgba(0,26,64,0.08);'), [
        flex(cid + ' Icon', 'width: 44px; min-width: 44px; height: 44px; padding: 0; border-radius: 22px; justify-content: center; align-items: center; background-color: %s;' % ('#ffffff' if dark else '#0160b4'), [img(cid + ' Glyph', icon, 'width: 19px;')]),
        flex(cid + ' Text', 'padding: 0; flex: 1 1 0; min-width: 0; flex-direction: column; gap: 2px;', [
            p(cid + ' Label', label, FB + 'font-size: 12px; color: %s; margin: 0;' % ('rgba(255,255,255,0.7)' if dark else '#747474')),
            p(cid + ' Value', value, FD + 'font-size: 15px; font-weight: %d; line-height: %s; color: %s; margin: 0; &:hover { color: %s; } transition: color 0.3s;' % (
                500 if dark else 600, '22px' if dark else '1.3', '#ffffff' if dark else '#003376', '#2c84cb' if dark else '#0160b4'), href=href),
        ]),
    ])


def info_section():
    return flex('Contact Info', 'padding: 110px 120px; gap: 56px; align-items: center; background-color: #f2f6fb; @media(--tablet) { flex-direction: column-reverse; align-items: stretch; padding: 80px 40px; gap: 44px; } @media(--mobile) { padding: 64px 20px; gap: 32px; }', [
        img('Contact Photo', 'ytcontact-photo-doha', 'width: 500px; flex: 0 0 auto; height: 560px; object-fit: cover; border-radius: 28px; @media(--tablet) { width: 100%; height: 420px; } @media(--mobile) { height: 280px; border-radius: 22px; }'),
        flex('Contact Text', 'padding: 0; flex: 1 1 0; flex-direction: column; gap: 18px; @media(--tablet) { flex: 0 0 auto; }', [
            flex('Contact Head', 'padding: 0; flex-direction: column; gap: 14px;', [
                kicker('Contact Eyebrow', 'Sales & Support'),
                title('Contact Title', 'Yutong Sales and ' + hl('Customer Support') + ' in the UAE', 'max-width: 600px; font-size: 42px; font-weight: 700; line-height: 52px; letter-spacing: 0; color: #00050b; @media(--mobile) { line-height: 38px; }'),
            ]),
            body('Contact Body 1', 'Al Khoory Automobiles is the official Yutong distributor in the UAE. Visit our showroom on Sheikh Zayed Road to explore Yutong coaches, city buses, school buses and e-buses, or speak to our sales and spare parts team by phone or e-mail.', 'line-height: 26px;', '#747474'),
            body('Contact Body 2', 'Yutong Bus is a globally renowned leader in the bus industry. In 2025 it achieved sales of 49,518 units and holds nearly 10% of the global market share.', 'line-height: 26px; margin-top: 8px;', '#747474'),
            flex('Contact Cards', 'padding: 4px 0 0; flex-direction: column; gap: 14px;', [
                flex('Contact Cards Row 1', 'padding: 0; gap: 14px; align-items: stretch; @media(--mobile) { flex-direction: column; }', [
                    info_card('Hotline', 'contact-ic-phone', 'Telephone', '04 3146235', 'tel:+97143146235'),
                    info_card('Email', 'contact-ic-mail', 'E-mail', 'info@subaru-uae.com', 'mailto:info@subaru-uae.com'),
                ]),
                flex('Contact Cards Row 2', 'padding: 0; gap: 14px; align-items: stretch; @media(--mobile) { flex-direction: column; }', [
                    info_card('WhatsApp', 'contact-ic-chat', 'WhatsApp &amp; online enquiry', 'Send us a message', URL['quote']),
                    info_card('Social', 'contact-ic-share', 'Follow Yutong', ' &middot; '.join('<a href="%s" target="_blank" rel="noopener">%s</a>' % (u, t) for t, u in YT_SOCIAL)),
                ]),
                info_card('Timing', 'q-clock', 'Showroom Timing', '09:30 AM to 8:00 PM &mdash; Open 7 days a week'),
                info_card('Address', 'contact-ic-pin', 'Showroom Address', 'Showroom 12, Sheikh Zayed Road<br>Al Quoz Industrial Area 3, Dubai, UAE', MAP_URL, dark=True),
            ]),
        ]),
    ], tag='section')


def form_section():
    return flex('Enquiry Form', 'padding: 110px 120px; background-color: #ffffff; position: relative; @media(--tablet) { padding: 80px 40px; } @media(--mobile) { padding: 64px 16px; }', [
        anchor('request-a-quote'),
        flex('Enquiry Card', 'padding: 0; align-items: stretch; background-color: #ffffff; border: 1px solid #e3e9f1; border-radius: 32px; overflow: hidden; box-shadow: 0 10px 28px rgba(0,26,64,0.08); @media(--tablet) { flex-direction: column; } @media(--mobile) { border-radius: 24px; }', [
            flex('Enquiry Visual', '''width: 460px; flex: 0 0 auto; min-height: 640px; padding: 32px; flex-direction: column; justify-content: flex-end;
                background-color: #ffffff; background-image: linear-gradient(180deg, rgba(7,43,82,0.2) 30%%, rgba(7,43,82,0.95) 100%%), %s;
                background-size: cover; background-position: center center;
                @media(--tablet) { width: 100%%; min-height: 420px; } @media(--mobile) { min-height: 380px; padding: 24px 20px; }''' % bg('contact-form-photo'), [
                flex('Enquiry Visual Text', 'padding: 0; flex-direction: column; gap: 12px;', [
                    kicker('Enquiry Eyebrow', 'Order &amp; Buy', '#ffffff'),
                    h('Enquiry Visual Title', 'We&rsquo;re Here to Help', 'h3', FD + 'font-size: 30px; font-weight: 700; line-height: 38px; color: #ffffff; margin: 0; @media(--mobile) { font-size: 26px; line-height: 34px; }'),
                    p('Enquiry Visual Text Body', 'Please share your details for prompt assistance. Tell us about your application, from e-bus and e-coach to city bus or coach, and the seating you need, and our UAE sales team will contact you as soon as possible.',
                      FB + 'font-size: 14px; line-height: 22px; color: rgba(255,255,255,0.8); margin: 0;'),
                ]),
            ]),
            flex('Enquiry Form Card', 'padding: 48px; flex: 1 1 0; min-width: 0; flex-direction: column; gap: 18px; justify-content: center; @media(--tablet) { flex: 0 0 auto; padding: 40px 32px; } @media(--mobile) { padding: 28px 20px; }', [
                h('Enquiry Form Title', 'Send us a message', 'h2', FD + 'font-size: 24px; font-weight: 600; color: #00050b; margin: 0;'),
                slot('Contact Form Slot'),
            ]),
        ]),
    ], tag='section')


SECTIONS = [
    ('contact-1', lambda: page_hero('contact', [('Home', 'home'), ('Contact', None)], 'Contact', HERO_TEXT, 'contact-hero', 600)),
    ('contact-2', lambda: [help_section()]),
    ('contact-3', lambda: [info_section()]),
    ('contact-4', lambda: [form_section(), footer()]),
]
