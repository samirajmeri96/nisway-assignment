<x-layout>
    <x-slot:title>{{ !empty($contact) ? 'Edit' : 'Add'  }} Contacts</x-slot:title>
    <section>
        <div class="d-flex justify-content-between gap-4">
            <h3>{{ !empty($contact) ? 'Edit' : 'Add'  }} Contacts</h3>
        </div>
        <hr />
        <form action="{{ !empty($contact) ? route('contact.update', $contact->id) : route('contact.store') }}" method="post" id="add-edit-contact-form">
            @csrf
            <div class="row">
                <div class="col-12 col-sm-6 mb-4">
                    <label class="form-label" for="first_name">First Name<span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" id="first_name" placeholder="First Name" maxlength="40" value="{{ old('first_name', $contact->first_name ?? '') }}"/>
                    @error('first_name')
                    <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-sm-6 mb-4">
                    <label class="form-label" for="last_name">Last Name<span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" id="last_name" placeholder="Last Name" maxlength="40" value="{{ old('last_name', $contact->last_name ?? '') }}"/>
                    @error('last_name')
                    <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-sm-6 mb-4">
                    <label class="form-label" for="phone">Phone<span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control digits-only" id="phone" placeholder="Phone" maxlength="13" minlength="13" value="{{ old('phone', $contact->phone ?? '') }}"/>
                    @error('phone')
                    <div class="d-block invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 text-end">
                    <a href="{{ route('contacts') }}" class="btn btn-secondary btn-sm me-2">Cancel</a>
                    <button type="submit" class="btn btn-success btn-sm">{{ !empty($contact) ? 'Update' : 'Save'  }}</button>
                </div>
            </div>
        </form>
    </section>
</x-layout>
