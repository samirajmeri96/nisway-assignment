<x-layout>
    <x-slot:title>Contacts</x-slot:title>
    <section>
        <div class="d-flex justify-content-between gap-4">
            <h3>Contacts</h3>
            <div>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#import-contact-modal">Import Contact</button>
                <a href="{{ route('contact.add') }}" class="btn btn-sm btn-success">Add Contact</a>
            </div>
        </div>
        <div class="mt-4">
            <table class="table table- table-striped table-hover table-bordered" id="contacts-table" data-url="{{ route('contacts.list') }}">
                <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Phone</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="4" class="text-center">No Data Found</td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal" role="dialog" id="import-contact-modal">
        <div class="modal-dialog">
            <form action="{{ route('contact.import') }}" enctype="multipart/form-data" id="import-contact-form">
                <div class="modal-content">
                    <div class="modal-header">Import contacts</div>
                    <div class="modal-body">
                        <div id="import-message-container"></div>
                        <div>
                            <input type="file" name="contacts" id="contacts" class="form-control" accept=".xml,application/xml,text/xml"/>
                        </div>
                        <div class="text-end">
                            <a href="{{ asset('storage/sample-contacts.xml') }}" target="_blank" download class="d-inline-block mt-4">Download Sample File</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-success">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <x-slot:script>
        <script type="application/javascript">
            window.addEventListener('DOMContentLoaded', function () {
                $(document).on('click', '.delete-contact', function () {
                    if (confirm("Are you sure, you want to delete this contact?")) {
                        let row = $(this).closest('tr');
                        let contactId = $(this).data('id');
                        let contactsTable = $('#contacts-table').DataTable();

                        $.ajax({
                            url: `{{ route('contact.delete') }}/${contactId}`,
                            type: "POST",
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.status === 'success') {
                                    let pageInfo = contactsTable.page.info();
                                    let currentPage = pageInfo.page;
                                    contactsTable.row(row).remove().draw(false);

                                    if (contactsTable.rows().count() === 0 && currentPage > 0) {
                                        contactsTable.page(currentPage - 1).draw(false);
                                    }

                                    alertMessage.success(response.message || 'The contact is deleted successfully!');
                                } else {
                                    alertMessage.error(response.message || 'Something went wrong, Please try again.');
                                }
                            },
                            error: function (jsXHR, status, errorMessage) {
                                alertMessage.error(errorMessage || 'Something went wrong, Please try again.');
                            }
                        });
                    }
                })
            })
        </script>
    </x-slot:script>
</x-layout>
