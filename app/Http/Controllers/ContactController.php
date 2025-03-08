<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use SimpleXMLElement;

class ContactController extends Controller
{
    protected $phoneRegex = '/^\+91[0-9]{10}$/';

    /**
     * Method used to render contacts list view.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|object
     */
    public function index()
    {
        return view('contact.index');
    }

    /**
     * Method used to return data for contact datatable.
     * @return mixed
     */
    public function list()
    {
        return app('datatables')->eloquent(Contact::query())->toJson();
    }

    /**
     * Method used to render add contact form.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|object
     */
    public function add()
    {
        return view('contact.add');
    }

    /**
     * Method used to render edit contact form.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|object
     */
    public function edit($id)
    {
        $contact = Contact::where('id', $id)->firstOrFail();
        return view('contact.add', [
            'contact' => $contact
        ]);
    }

    /**
     * Method used to add new contact in db.
     * @param Request $request
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|object|void
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:40'],
            'last_name' => ['required', 'string', 'max:40'],
            'phone' => ['required', 'string', function ($attribute, $value, $fail) {
                $number = preg_match($this->phoneRegex, $value);
                if (!$number) {
                    $fail(__('validation.custom.phone.invalid', ['phone' => 'Phone']));
                }
            }, 'unique:contacts,phone']
        ]);

        $newContact = new Contact();
        $newContact->first_name = $request->first_name;
        $newContact->last_name = $request->last_name;
        $newContact->phone = $request->phone;
        $newContact->save();

        Session::flash('success', 'Contact added successfully!');
        return redirect(route('contacts'));
    }

    /**
     * Method used to update existing contact in db.
     * @param Request $request
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|object|void
     */
    public function update(Request $request, $id)
    {
        $updateContact = Contact::where('id', $id)->firstOrFail();
        $request->validate([
            'first_name' => ['required', 'string', 'max:40'],
            'last_name' => ['required', 'string', 'max:40'],
            'phone' => ['required', 'string', function ($attribute, $value, $fail) {
                $number = preg_match($this->phoneRegex, $value);
                if (!$number) {
                    $fail(__('validation.custom.phone.invalid', ['phone' => 'Phone']));
                }
            }, 'unique:contacts,phone,'.$updateContact->id]
        ]);

        $updateContact->first_name = $request->first_name;
        $updateContact->last_name = $request->last_name;
        $updateContact->phone = $request->phone;
        $updateContact->save();

        Session::flash('success', 'Contact updated successfully!');
        return redirect(route('contacts'));
    }

    /**
     * Method used to delete contact.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $updateContact = Contact::where('id', $id)->firstOrFail();
        if ($updateContact->delete()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Contacts deleted successfully!'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong, please try again!'
            ]);
        }
    }

    /**
     * Method used to import contact from an XML file.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function importContacts(Request $request)
    {
        $request->validate([
            'contacts' => ['required', 'file', 'mimes:xml']
        ]);

        $contacts = $request->file('contacts');
        $xmlContacts = file_get_contents($contacts);
        $xml = new SimpleXMLElement($xmlContacts);
        if (count($xml->contact) > 0) {
            $insertContacts = [];
            $errors = [];
            $i = 0;
            foreach ($xml->contact as $contact) {
                $i++;
                $firstName = (string) $contact->firstName;
                $lastName = (string) $contact->lastName;
                $phoneNumber = (string) $contact->phone;
                $phoneNumber = !empty($phoneNumber) ? str_replace(' ', '', $phoneNumber) : '';

                if (empty($firstName)) {
                    $errors[] = 'The contact number #'.$i.': '.__('validation.required', ['attribute' => 'First Name']);
                } else if(strlen($firstName) > 40) {
                    $errors[] = 'The contact number #'.$i.': '.__('validation.max.string', ['attribute' => 'First Name', 'max' => 40]);
                }
                if (empty($lastName)) {
                    $errors[] = 'The contact number #'.$i.': '.__('validation.required', ['attribute' => 'Last Name']);
                } else if(strlen($lastName) > 40) {
                    $errors[] = 'The contact number #'.$i.': '.__('validation.max.string', ['attribute' => 'Last Name', 'max' => 40]);
                }
                if (empty($phoneNumber)) {
                    $errors[] = 'The contact number #'.$i.': '.__('validation.required', ['attribute' => 'Phone']);
                } else if(!preg_match($this->phoneRegex, $phoneNumber)) {
                    $errors[] = 'The contact number #'.$i.': '.__('validation.custom.phone.invalid', ['attribute' => 'Phone']);
                } else if(in_array($phoneNumber, array_column($insertContacts, 'phone'))) {
                    $errors[] = 'The contact number #'.$i.': '.__('validation.distinct', ['attribute' => 'Phone']);
                } else {
                    $isExist = Contact::where('phone', $phoneNumber)->exists();
                    if ($isExist) {
                        $errors[] = 'The contact number #'.$i.': '.__('validation.custom.phone.duplicate', ['attribute' => 'Phone Number']);
                    }
                }

                if (empty($errors)) {
                    $insertContacts[] = [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'phone' => $phoneNumber,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
            }

            if (!empty($errors)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please solve the errors in file.',
                    'validation_errors' => $errors
                ]);
            }

            Contact::insert($insertContacts);

            return response()->json([
                'status' => 'success',
                'message' => 'Contacts imported successfully!'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'The file is empty, Please create a file as per the sample file.'
            ]);
        }
    }
}
