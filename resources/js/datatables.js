import DataTable from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import moment from "moment";
window.addEventListener('DOMContentLoaded', function () {
    const contactsTable = $('#contacts-table');
    if (contactsTable && contactsTable.data('url')) {
        let contactsDataTable = new DataTable(`#${contactsTable.attr('id')}`, {
            processing: true,
            serverSide: true,
            ajax: {
                url: contactsTable.data('url')
            },
            order: [[3, 'desc']],
            columns: [
                {
                    data: 'first_name',
                    name: 'first_name',
                },
                {
                    data: 'last_name',
                    name: 'last_name',
                },
                {
                    data: 'phone',
                    name: 'phone',
                },
                {
                    data: null,
                    name: 'created_at',
                    render: function (row) {
                        return row?.created_at ? moment(row?.created_at).format('DD MMMM, YYYY hh:mm A') : '-';
                    }
                },
                {
                    data: null,
                    name: 'action',
                    searchable: false,
                    orderable: false,
                    render: function(row) {
                        return '<div class="d-flex gap-1">' +
                            '<a href="/contact/edit/'+row.id+'" class="btn btn-primary btn-sm">Edit</a>' +
                            '<button type="button" data-id="'+row.id+'" class="btn btn-danger btn-sm delete-contact">Delete</button>' +
                            '</div>';
                    }
                }
            ]
        });
    }
})
