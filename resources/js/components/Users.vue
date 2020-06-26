<template>
    <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users Table</h3>

                <div class="card-tools">
                  <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#addNew"><i class="fas fa-user-plus"></i></button>
                </div>

                <div class="modal fade" id="addNew">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h4 class="modal-title">Add New</h4>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <form @submit.prevent="createUser">
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
                              <button type="submit" class="btn btn-primary">Create</button>
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
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(user, i) in users" :key="user.id">
                      <td>{{ i+1 }}</td>
                      <td>{{ user.name }}</td>
                      <td>{{ user.email }}</td>
                      <td><span class="tag tag-success">{{ user.type }}</span></td>
                      <td>
                        <a href="#" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                        <a href="#" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                users: [],
                form: new Form({
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
                axios.get('api/user').then(
                                        ({ data }) => (this.users = data)
                                    );
            },
            createUser() {
                this.$Progress.start();
                this.form.post('api/user');
                this.$Progress.finish();
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        created() {
            this.loadUsers();
        }
    }
</script>
