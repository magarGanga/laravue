<template>
<v-app id="inspire">
    <v-content>
        <v-container class="fill-height" fluid>
            <v-row align="center" justify="center">
                <v-col cols="12" sm="8" md="4">
                    <v-card class="elevation-12">
                        <v-toolbar color="error" dark flat>
                            <v-toolbar-title>Login Form</v-toolbar-title>
                            <v-spacer />

                        </v-toolbar>
                        <v-card-text>

                            <v-progress-linear :active="loading" :indeterminate="loading" absolute top color="white accent-8"></v-progress-linear>

                            <v-form ref="form" v-model="valid">
                                <v-text-field color="error" label="Login" name="login" v-model="email" :rules="emailRules" prepend-icon="mdi-account-circle-outline" type="email" required />

                                <v-text-field color="error" id="password" label="Password" name="password" v-model="password" prepend-icon="mdi-account-lock-outline" type="password" required />
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer />
                            <v-btn color="error" :disabled="!valid" block @click="login">Login</v-btn>
                        </v-card-actions>
                    </v-card>

                    <v-snackbar v-model="snackbar">
                        {{ text }}
                        <v-btn color="pink" text @click="snackbar = false">
                            Close
                        </v-btn>
                    </v-snackbar>
                </v-col>
            </v-row>
        </v-container>
    </v-content>
</v-app>
</template>

<script>
export default {
    data() {
        return {
            email: '',
            emailRules: [
                v => !!v || 'E-mail is required',
                v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
            ],
            password: '',
            passwordRules: [
                v => !!v || 'Password is required',
            ],
            loading: false,
            snackbar: false,
            text: '',
            valid: true,

        }
    },

    created() {
        this.$vuetify.theme.dark = true;
    },

    methods: {
        login: function () {

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

            axios.post('/api/login', {
                    'email': this.email,
                    'password': this.password
                })
                .then(res => {
                    localStorage.setItem('token', res.data.token)
                    localStorage.setItem('LoggedIn', true)
                    this.$router.push('/admin').then(res => console.log("LoggedIn Successfully")).catch(console.log(err))
                }).catch(err => {
                    this.text = err.response.data.status
                    this.snackbar = true;
                });

        }
    }
}
</script>

<style scoped>

</style>
