#!/usr/bin/env python3
"""Create/update the Contact Form 7 forms from the client copy (quote, service booking, spare parts).

usage: forms.py   -> prints {key: form_id}; ids are cached in forms.json next to this file.
CF7 REST only persists with "context": "save" and a complete mail object.
"""
import json, os, subprocess
import models_data as M
HERE = os.path.dirname(os.path.abspath(__file__))
AUTH = json.load(open(os.path.expanduser('~/.claude.json')))['mcpServers']['my-blog-elementor']['headers']['Authorization']
API = 'https://soharon.co.uk/yutong/wp-json/contact-form-7/v1/contact-forms'
CACHE = os.path.join(HERE, 'forms.json')

ALL_MODELS = [m['name'] for m in M.models()]
SERVICE_MODELS = sorted(ALL_MODELS, key=lambda n: (n[0], n))  # alphabetical like the copy doc


def opts(values):
    return ' '.join('"%s"' % v for v in values)


def row(*fields):
    return '<div class="yt-row">%s</div>' % ''.join(fields)


def field(label, tag):
    return '<label class="yt-field">%s%s</label>' % (label, tag)


FORMS = {
    'quote': {
        'title': 'Yutong Quote Request',
        'form': '\n'.join([
            row(field('Full Name*', '[text* full-name placeholder "Enter your full name"]'),
                field('Company Name*', '[text* company placeholder "Enter your company name"]')),
            row(field('Job Title', '[text job-title placeholder "Enter your job title"]'),
                field('Email Address*', '[email* your-email placeholder "Enter your email address"]')),
            row(field('Phone Number*', '[tel* phone placeholder "Enter your contact number"]'),
                field('Number of Vehicles Required', '[number vehicles min:1 placeholder "Enter quantity"]')),
            row(field('Vehicle Category*', '[select* category class:yt-category-select first_as_label "Select a category" %s "Not Sure Yet"]' % opts([c[1] for c in M.CATEGORIES])),
                field('Model of Interest', '[select model class:yt-model-select first_as_label "Select a model" %s "Not Sure Yet"]' % opts(ALL_MODELS))),
            field('Intended Use', '[select intended-use first_as_label "Select an option" "Tourism & Travel" "Public Transportation" "Corporate Transportation" "Hospitality & Shuttle Services" "Other"]'),
            field('Additional Requirements', '[textarea requirements placeholder "Tell us more about your transport requirements."]'),
            '[submit class:yt-submit "Request a Quote"]',
        ]),
        'subject': '[_site_title] quote request: [category] [model] — [company]',
        'body': 'Full name: [full-name]\nCompany: [company]\nJob title: [job-title]\nEmail: [your-email]\nPhone: [phone]\nVehicle category: [category]\nModel of interest: [model]\nNumber of vehicles: [vehicles]\nIntended use: [intended-use]\n\n[requirements]',
        'ok': 'Thank you for your enquiry. Your request has been received. A member of the Yutong UAE team will contact you regarding your requirements.',
    },
    'service': {
        'title': 'Yutong Service Booking',
        'form': '\n'.join([
            row(field('Full Name*', '[text* full-name placeholder "Enter your full name"]'),
                field('Company Name*', '[text* company placeholder "Enter your company name"]')),
            row(field('Phone Number*', '[tel* phone placeholder "Enter your contact number"]'),
                field('Email Address', '[email your-email placeholder "Enter your email address"]')),
            row(field('Vehicle Model*', '[select* model first_as_label "Select your Yutong model" %s]' % opts(SERVICE_MODELS)),
                field('Vehicle Registration Number', '[text registration placeholder "Enter vehicle registration number"]')),
            row(field('Service Required*', '[select* service first_as_label "Select an option" "Scheduled Maintenance" "Repair / Technical Support" "Spare Parts Requirement" "General Enquiry" "Other"]'),
                field('Preferred Date', '[date preferred-date placeholder "Select preferred date"]')),
            '<div class="yt-field">Preferred Service Centre[radio service-centre use_label_element default:1 "Dubai" "Abu Dhabi"]</div>',
            field('Additional Details', '[textarea details placeholder "Tell us more about your service requirement."]'),
            '[submit class:yt-submit "Request Service"]',
        ]),
        'subject': '[_site_title] service booking: [model] — [service-centre]',
        'body': 'Full name: [full-name]\nCompany: [company]\nPhone: [phone]\nEmail: [your-email]\nVehicle model: [model]\nRegistration: [registration]\nService required: [service]\nPreferred centre: [service-centre]\nPreferred date: [preferred-date]\n\n[details]',
        'ok': 'Thank you. Your service request has been received. A member of the Yutong UAE team will contact you shortly to confirm your appointment details.',
    },
    'parts': {
        'title': 'Yutong Spare Parts Enquiry',
        'form': '\n'.join([
            row(field('Full Name*', '[text* full-name placeholder "Enter your full name"]'),
                field('Company Name*', '[text* company placeholder "Enter your company name"]')),
            row(field('Phone Number*', '[tel* phone placeholder "Enter your contact number"]'),
                field('Email Address', '[email your-email placeholder "Enter your email address"]')),
            row(field('Vehicle Model*', '[select* model first_as_label "Select your model" %s]' % opts(SERVICE_MODELS)),
                field('Vehicle Registration Number', '[text registration placeholder "Enter vehicle registration number"]')),
            field('Part Required', '[text part placeholder "Enter part name / description"]'),
            field('Additional Details', '[textarea details placeholder "Tell us more about your requirement."]'),
            '[submit class:yt-submit "Submit Enquiry"]',
        ]),
        'subject': '[_site_title] spare parts enquiry: [model] — [part]',
        'body': 'Full name: [full-name]\nCompany: [company]\nPhone: [phone]\nEmail: [your-email]\nVehicle model: [model]\nRegistration: [registration]\nPart required: [part]\n\n[details]',
        'ok': 'Thank you. Your spare parts enquiry has been received. A member of the Yutong UAE team will contact you regarding your requirements.',
    },
}


def rest(method, url, body=None):
    cmd = ['curl', '-sS', '-m', '120', '-X', method, '-H', 'Authorization: ' + AUTH, '-H', 'Content-Type: application/json', url]
    if body is not None:
        cmd += ['--data-binary', '@-']
    return json.loads(subprocess.run(cmd, input=json.dumps(body) if body is not None else None, capture_output=True, text=True).stdout)


def main():
    ids = json.load(open(CACHE)) if os.path.exists(CACHE) else {}
    for key, f in FORMS.items():
        body = {'title': f['title'], 'locale': 'en_US', 'form': f['form'], 'context': 'save',
                'mail': {'active': True, 'subject': f['subject'], 'sender': '[_site_title] <wordpress@soharon.co.uk>',
                         'recipient': '[_site_admin_email]', 'body': f['body'] + '\n\n-- \nSent from [_url]',
                         'additional_headers': 'Reply-To: [your-email]', 'attachments': '', 'use_html': False, 'exclude_blank': False},
                'messages': {'mail_sent_ok': f['ok']}}
        r = rest('POST', API + ('/%d' % ids[key] if key in ids else ''), body)
        ids[key] = r['id']
        got = rest('GET', API + '/%d' % ids[key])
        ok = f['title'] == got['title'] and 'yt-row' in got['properties']['form']['content']
        print(key, ids[key], 'saved' if ok else 'NOT SAVED', got.get('config_errors') or '')
    json.dump(ids, open(CACHE, 'w'), indent=1)


if __name__ == '__main__':
    main()
