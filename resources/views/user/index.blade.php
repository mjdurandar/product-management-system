<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role == 'user' ? 'User' : 'Admin' }}</td>
                                    <td>
                                        <button onclick="openEditModal({{ $user->id }}, '{{ $user->role }}')" class="btn btn-primary">Edit</button>
                                        <button onclick="deleteRole({{ $user->id }})" class="btn btn-danger">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editRoleForm">
                        <div class="form-group">
                            <label for="roleSelect">Role</label>
                            <select class="form-control" id="roleSelect">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <input type="hidden" id="editUserId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveRole()">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(userId, currentRole) {
            document.getElementById('editUserId').value = userId;
            document.getElementById('roleSelect').value = currentRole;
            $('#editRoleModal').modal('show');
        }

        function saveRole() {
            const userId = document.getElementById('editUserId').value;
            const newRole = document.getElementById('roleSelect').value;

            fetch(`/user/${userId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ role: newRole })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Saved!', data.success, 'success');
                    $('#editRoleModal').modal('hide');
                    location.reload(); 
                } else {
                    Swal.fire('Error!', 'Failed to update role.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'There was an error updating the role.', 'error');
            });
        }

        function deleteRole(userId) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Delete role for user ID: ${userId}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Deleted!', 'The user role has been deleted.', 'success');
                }
            });
        }
    </script>
</x-app-layout>
