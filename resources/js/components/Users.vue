<template>
    <div class="container">
        <div class="row" v-if="$gate.isAdminOrAuthor()">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users Table</h3>

                <div class="card-tools">
                  <button class="btn btn-success btn-lg" @click="newModal"><i class="fas fa-user-plus"></i></button>
                </div>

                <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h4 class="modal-title">Add New</h4>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <form id="userForm" @submit.prevent="editMode ? updateUser() : createUser()">
                            <div class="modal-body">
                              <div class="form-group">
                                  <input type="text" name="name" placeholder="Name" class="form-control" v-model="form.name" :class="{ 'is-invalid' : form.errors.has('name') }">
                                  <has-error :form="form" field="name"></has-error>
                              </div>
                              <div class="form-group">
                                  <input type="email" name="email" placeholder="Email" class="form-control" v-model="form.email" :class="{ 'is-invalid' : form.errors.has('email') }">
                                  <has-error :form="form" field="email"></has-error>
                              </div>
                              <div class="form-group">
                                  <textarea name="bio" rows="4" placeholder="Bio" class="form-control" v-model="form.bio" :class="{ 'is-invalid' : form.errors.has('bio') }"></textarea>
                                  <has-error :form="form" field="bio"></has-error>
                              </div>
                              <div class="form-group">
                                  <select name="type" class="custom-select" v-model="form.type" :class="{ 'is-invalid' : form.errors.has('type') }">
                                      <option disabled="" selected="">Select User Role</option>
                                      <option value="admin">Admin</option>
                                      <option value="user">Standard User</option>
                                      <option value="author">Author</option>
                                  </select>
                                  <has-error :form="form" field="type"></has-error>
                              </div>
                              <div class="form-group">
                                  <input type="password" name="password" placeholder="Password" class="form-control" v-model="form.password" :class="{ 'is-invalid' : form.errors.has('password') }">
                                  <has-error :form="form" field="password"></has-error>
                              </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                              <button v-if="editMode" type="submit" class="btn btn-warning">Update</button>
                              <button v-else="" type="submit" class="btn btn-primary">Create</button>
                            </div>
                        </form>
                      </div>
                      <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                  </div>
                  <!-- /.modal -->

              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>Sl No.</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Type</th>
                      <th>Registered At</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(user, i) in users" :key="user.id">
                      <td>{{ i+1 }}</td>
                      <td>{{ user.name }}</td>
                      <td>{{ user.email }}</td>
                      <td><span class="tag tag-success">{{ user.type }}</span></td>
                      <td>{{ user.created_at }}</td>
                      <td>
                        <button @click="editModal(user)" class="btn btn-warning"><i class="fa fa-edit"></i></button>
                        <button @click="deleteUser(user.id)" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <button @click.prevent="printMe" class="btn btn-lg btn-primary"><i class="fas fa-print mr-2"></i>Print</button>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <div v-else="">
          <not-found></not-found>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                editMode: false,
                users: [],
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: '',
                })
            }
        },
        methods: {

            loadUsers(){
              if (this.$gate.isAdminOrAuthor()) {
                axios.get('api/user').then((success) => (this.users = success.data));
              }
            },
            newModal() {
                this.form.reset();
                this.editMode = false;
                $('.modal-title').text('Add New User');
                $('#formModal').modal('show');
            },
            createUser() {
                this.$Progress.start();
                this.form.post('api/user')
                .then((success) => {
                    this.loadUsers();
                    $('#formModal').modal('hide');
                    $('.modal-backdrop').hide();
                    Toast.fire({
                      type: 'success',
                      title: success.data
                    });
                    this.form.reset();
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.$Progress.fail();
                    Toast.fire({
                      type: 'error',
                      title: 'Something wrong!'
                    });
                });
            },
            editModal(user) {
                this.form.fill(user);
                this.editMode = true;
                $('.modal-title').text('Edit User');
                $('#formModal').modal('show');
            },
            updateUser(id) {
                this.$Progress.start();
                this.form.put('api/user/' +this.form.id)
                .then((success) => {
                    this.loadUsers();
                    $('#formModal').modal('hide');
                    $('.modal-backdrop').hide();
                    Toast.fire({
                      type: 'success',
                      title: success.data
                    });
                    this.form.reset();
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.$Progress.fail();
                    Toast.fire({
                      type: 'error',
                      title: 'Something wrong!'
                    });
                });
            },
            deleteUser(id) {
                Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                  if (result.value) {
                    this.$Progress.start();
                    axios.delete('api/user/' +id)
                    .then((success) => {
                        this.loadUsers();
                        Toast.fire({
                          type: 'success',
                          title: success.data
                        });
                        this.$Progress.finish();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                        Toast.fire({
                          type: 'error',
                          title: 'Something wrong'
                        });
                    });
                  }
                })
            },
            printMe() {
              window.print();
            }

        },
        mounted() {
            console.log('Component mounted.');
            $('.pageTitle').text('Users');
        },
        created() {
            this.loadUsers();
            // setInterval(() => this.loadUsers(), 3000);
        }
    }
</script>
