<template>
<v-app id="inspire">
    <v-data-table item-key="name"
            class="elevation-1"
            :loading="loading"
            loading-text="Loading... Please wait"
            :headers="headers"
            @pagination="paginate"
            :server-items-length="users.total"
            :options.sync="options"
            :items="users.data"
            :items-per-page=5
            show-select
            @input="selectAll"
            :footer-props="{
            itemsPerPageOptions: [5,10,15],
            itemsPerPageText: 'users Per Page',
            'show-current-page': true,
            'show-first-last-page': true,

            }">
        <template v-slot:top>
            <v-toolbar flat color="dark">
                <v-toolbar-title>User Management System</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>
                <v-dialog v-model="dialog" max-width="500px">
                    <template v-slot:activator="{ on }">
                        <v-btn color="error" dark class="mb-2" v-on="on">Add New User</v-btn>
                        <v-btn color="error" dark class="mb-2 mr-2" @click="deleteAll">Delete</v-btn>

                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{ formTitle }}</span>
                        </v-card-title>

                        <v-form v-model="valid" method="post" v-on:submit.stop.prevent="save">
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" sm="12">
                                            <v-text-field :rules="[rules.required, rules.min]" v-model="editedItem.name" color="error" label="Name"></v-text-field>
                                        </v-col>

                                    </v-row>

                                    <v-row v-if="editedIndex === -1">
                                        <v-col cols="12" sm="12">
                                            <v-text-field :rules="[rules.required]" type="password" color="error" v-model="editedItem.password" label="Type Password"></v-text-field>
                                        </v-col>

                                        <v-col cols="12" sm="12">
                                            <v-text-field :rules="[rules.required, passwordMatch]" type="password" color="error" v-model="editedItem.rpassword" label="Retype Password"></v-text-field>
                                        </v-col>

                                        <v-col cols="12" sm="12">
                                            <v-text-field  type="email" :success-messages="success" :error-messages="error" :rules="[rules.required, rules.validEmail]" @blur="checkEmail" v-model="editedItem.email" color="error" label="Email"></v-text-field>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col cols="12" sm="12">
                                            <v-select :rules="[rules.required]" :items="roles" v-model="editedItem.role" color="error" label="Select Role"></v-select>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="error darken-1" text @click="close">Cancel</v-btn>
                                <v-btn type="submit" :disabled="!valid" color="error darken-1" text @click="save">Save</v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-dialog>
            </v-toolbar>
            <v-row>
                <v-col cols="12">
                    <v-text-field @input="searchIt" label="Search..." class="mx-4"></v-text-field>
                </v-col>
            </v-row>
        </template>

        <template v-slot:item.role="{item}">
            <v-edit-dialog
                large
                block
                persistent
                :return-value.sync="item.role"
                @save="updateRole(item)"
            >
                {{ item.role }}
                    <template v-slot:input>
                        <h2>Change Role</h2>
                    </template>

                    <template v-slot:input>
                        <v-col cols="12" sm="12">
                            <v-select :rules="[rules.required]" :items="roles" v-model="item.role" color="error" label="Select Role"></v-select>
                        </v-col>
                    </template>

            </v-edit-dialog>
        </template>

        <template v-slot:item.photo="{ item }">
            <v-edit-dialog>
                <v-list-item-avatar>
                    <v-img
                        :src="`../storage/${item.photo}`"
                        aspect-ratio="1"
                        class="grey lighten-2"
                        max-width="50"
                        max-height="50">
                    </v-img>
                </v-list-item-avatar>
                <template v-slot:input>
                    <v-file-input
                        v-model="editedItem.photo"
                        labe="Select File"
                        accept="image/jpg, image/png, image/bmp, image/jpeg"
                        placeholder="Upload Avatar"
                        @change="uploadPhoto(item)"
                     />
                </template>

            </v-edit-dialog>

        </template>

        <template v-slot:item.actions="{ item }">
            <v-icon small class="mr-2" color="success" @click="editItem(item)">mdi-content-save-edit-outline</v-icon>
            <v-icon small @click="deleteItem(item)" color="error"> mdi-delete </v-icon>
        </template>

        <template v-slot:no-data>
            <v-btn color="error" @click="initialize">Reset</v-btn>
        </template>

    </v-data-table>
    <v-snackbar v-model="snackbar">
        {{ text }}
        <v-btn color="pink" text @click="snackbar = false">
            Close
        </v-btn>
    </v-snackbar>
</v-app>
</template>

<script>
export default {
    data: () => ({
        valid: true,
        dialog: false,
        loading: false,
        snackbar: false,
        selected: [],
        success: '',
        error: '',
        text: '',
        options:{
            sortBy: ['name'],
            sortDesc: [true]

        },
        headers: [{
                text: '#',
                align: 'start',
                sortable: false,
                value: 'id',
            },

            {
                text: 'Name',
                sortable: false,

                value: 'name'
            },

            {
                text: 'Email',
                sortable: false,
                value: 'email'
            },

            {
                text: 'Role',
                sortable: false,
                value: 'role'
            },

            {
                text: 'Photo',
                sortable: false,
                value: 'photo'
            },

            {
                text: 'Created At',
                sortable: false,
                value: 'created_at'
            },

            {
                text: 'Updated At',
                sortable: false,
                value: 'created_at'
            },

            {
                text: 'Actions',
                sortable: false,
                value: 'actions',
            },

        ],
        users: [],
        roles: [],
        rules: {
            required: v => !!v || 'This Field is Required',
            min: v => v.length >= 5 || 'Minimum 5 Character Required',
            validEmail: v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
        },
        editedIndex: -1,
        editedItem: {
            id: '',
            name: '',
            email: '',
            role: '',
            created_at: '',
            updated_at: '',
            photo: null

        },
        defaultItem: {
            id: '',
            name: '',
            email: '',
            role: '',
            photo: '',
            password: '',
            rpassword: '',
            created_at: '',
            updated_at: '',

        },
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
        },

        passwordMatch() {
            return this.editedItem.password != this.editedItem.rpassword ? 'Password Does Not Match' : true
        },

    },

    watch: {
        dialog(val) {
            val || this.close()
        },
    },

    created() {
        this.initialize()
    },

    methods: {
        uploadPhoto(item){
            if(this.editedItem.photo != null){
                const index = this.users.data.indexOf(item);
                let formData = new FormData();
                formData.append('photo', this.editedItem.photo, this.editedItem.photo.name)
                formData.append('user', item.id)
                axios.post('/api/user/photo', formData)
                .then(res => {
                      this.users.data[index].photo = res.data.user.photo
                      this.editedItem.photo = null
                })
                .catch(err => console.log(err.response))
            }

        },

        updateRole(item){
            const index = this.users.data.indexOf(item)
            axios.post('api/user/role', {'role': item.role, 'user': item.id})
            .then( res => {
                this.text = res.data.user.name + "'s Role Updated to " + res.data.user.role
                this.snackbar = true
            })
            .catch( error =>{
                this.text = error.response.data.user.name + "'s Role cannot be Updated to " + error.response.data.user.role
                this.users.data[index].role = error.response.data.user.role
                this.snackbar = true
                console.dir(error.response)
            })
        },

         checkEmail(){
           if(/.+@.+\..+/.test(this.editedItem.email)){
               axios.post('/api/email/verify', {'email': this.editedItem.email} )
               .then(res =>{
                   this.success = res.data.message
                   this.error = '';
               })
               .catch(err => {
                   this.success = ''
                   this.error = 'Email Already Exists'
               })
           }
        },

        selectAll(e) {
            this.selected = [];
            if (e.length > 0) {
                this.selected = e.map(val => val.id)
            }
        },

        deleteAll() {
            let decide = confirm('Are you sure you want to delete this items?')
            if (decide) {
                axios.post('/api/users/delete', {
                        'users': this.selected
                    })
                    .then(res => {
                        this.text = "Records Deleted Successfully";

                        this.selected.map(val => {
                            const index = this.users.data.indexOf(val)
                            this.users.data.splice(index, 1)
                        })
                        this.snackbar = true
                    })
                    .catch(err => console.log(err.response))
            }
        },

        // searchIt(e){
        //    if(e.length > 3){
        //        axios.get(`/api/users/${e}`)
        //        .then(res => this.users = res.data.users)
        //        .catch(err => console.dir(err.response))
        //    }
        //    if(e.length <= 0){
        //         axios.get(`/api/users`)
        //        .then(res => this.users = res.data.users)
        //        .catch(err => console.dir(err.response) )
        //    }
        // },
        searchIt(e) {
            if (e.length > 3) {
                axios.get(`/api/roles/${e}`)
                    .then(res => this.roles = res.data.roles)
                    .catch(err => console.dir(err.response))
            }
            if (e.length <= 0) {
                axios.get(`/api/roles`)
                    .then(res => this.roles = res.data.roles)
                    .catch(err => console.dir(err.response))
            }
        },

        paginate(e) {
            const sortBy =
                this.options.sortBy.length == 0 ? "name" : this.options.sortBy[0];
            const orderBy =
                this.options.sortDesc.length > 0 && this.options.sortDesc[0]
                    ? "asc"
                    : "desc";
            axios.get(`/api/users?page=${e.page}`, {
                    params: {
                        'per_page': e.itemsPerPage,
                       'sort_by' : sortBy,
                        'order_by' : orderBy
                    }
                })
                .then(res => {
                    this.users = res.data.users
                    this.roles = res.data.roles
                    //    console.dir(res.data.users)
                })
                .catch(err => {
                    //checking token, if not redirect to login form
                    if (err.response.status == 401)
                        localStorage.removeItem('token');
                    this.$router.push('/login');
                })

        },

        initialize() {
            // Add a request interceptor
            axios.interceptors.request.use((config) => {
                this.loading = true;
                return config;
            }, (error) => {
                // Do something with request error
                this.loading = false;
                return Promise.reject(error);
            });

            // Add a response interceptor
            axios.interceptors.response.use((response) => {
                // Any status code that lie within the range of 2xx cause this function to trigger
                // Do something with response data
                this.loading = false;
                return response;
            }, error => {
                // Any status codes that falls outside the range of 2xx cause this function to trigger
                // Do something with response error
                this.loading = false;
                return Promise.reject(error);
            });

            //get data from users database
            //edited for pagination
        },

        editItem(item) {
            this.editedIndex = this.users.data.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem(item) {
            const index = this.users.data.indexOf(item)
            let decide = confirm('Are you sure you want to delete this item?')
            if (decide) {
                axios.delete('/api/users/' + item.id)
                    .then(res => {
                        this.text = "Record Deleted Successfully";
                        this.snackbar = true
                        this.users.data.splice(index, 1)
                    })
                    .catch(err => console.log(err.response))
            }
        },

        close() {
            this.dialog = false
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            }, 300)
        },

        save() {

            if (this.editedIndex > -1) {
                const index = this.editedIndex
                axios.put('/api/users/' + this.editedItem.id, this.editedItem)
                    .then(res => {
                        this.text = "Record Updated Successfully";
                        this.snackbar = true;
                        Object.assign(this.users.data[index], res.data.user)
                    })
                    .catch(err => {

                        console.log(err.response)
                        this.text = "Error Updating Record"
                        this.snackbar = true;
                    })
                // Object.assign(this.users[this.editedIndex], this.editedItem)

            } else {
                console.log(this.editedItem);
                axios.post('/api/users', this.editedItem)
                    .then(res => {
                        this.text = "Record Added Successfully";
                        this.snackbar = true;
                        this.users.data.push(res.data.user)

                    })
                    .catch(err => {
                        console.dir(err.response)
                        this.text = "Error Insertings Record"
                        this.snackbar = true;
                    })
            }
            this.close()
        },
    },
}
</script>

<style scoped>

</style>
