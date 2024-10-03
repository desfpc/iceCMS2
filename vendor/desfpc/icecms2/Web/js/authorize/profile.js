export default {
    data() {
        return {
            user: {
                phone: null,
                telegram: null,
                language: null,
                languages: null,
                name: null,
                nikname: null,
                status: null,
                role: null,
                rating: null,
                avatar: null,
                created_time: null,
                sex: null,
                contacts: {},
            },
            socialData: {
                friends: {
                    page: 0,
                    total: 0,
                    entries: [],
                },
                subscribers: {
                    page: 0,
                    total: 0,
                    entries: [],
                },
                subscriptions: {
                    page: 0,
                    total: 0,
                    entries: [],
                },
                limit: 20,
            },
            alert: {
                show: false,
                class: 'alert',
                message: '',
            },
            languages: [
                { text: 'English', value: 'en' },
                { text: 'Русский', value: 'ru' },
                { text: 'Српски', value: 'rs' },
                { text: 'ქართული', value: 'ge' },
            ],
            sexes: [
                { text: 'Male', value: 'male' },
                { text: 'Female', value: 'female' },
                { text: 'Other', value: 'other' },
            ],
            contacts: [ 'Country', 'City', 'Address', 'Zip', 'Twitter', 'Instagram', 'LinkedIn',
                'YouTube', 'Discord', 'Website', 'Blog', 'Other' ],
            password: {
                old: '',
                new: '',
            },
            ifError: true,
            ifMessage: '',
        }
    },

    methods: {
        onUploadFiles(event) {
            const files = event.target.files
            const formData = new FormData()
            formData.append('file', files[0])

            axios.post('/api/v1/profile/avatar', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                if (response.data.success === true) {
                    this.user.avatar = response.data.data.url
                    this.alert.class = 'alert alert-success sticky-top'
                    this.alert.message = 'Avatar updated'
                    this.alert.show = true
                } else {
                    this.alert.class = 'alert alert-danger sticky-top'
                    this.alert.message = 'Error in avatar update'
                    this.alert.show = true
                }
            })
        },

        changePassword() {
            axios.post('/api/v1/profile/change-password', this.password).then(response => {
                if (response.data.success === true) {
                    this.alert.class = 'alert alert-success sticky-top'
                    this.alert.message = 'Password updated'
                    this.alert.show = true
                } else {
                    this.alert.class = 'alert alert-danger sticky-top'
                    this.alert.message = 'Error in password update'
                    this.alert.show = true
                }
            })
        },

        setLanguages(key, data) {
            this.user.languages = data;
        },

        showUpdateError(message) {

            if (message !== '') {
                this.alert.class = 'alert alert-danger sticky-top'
                this.alert.message = message
                this.alert.show = true
            }
        },

        save() {
            if (this.$checkValidation()) {
                axios.post('/api/v1/profile/update', this.user).then(response => {
                    if (response.data.success === true) {
                        this.ifError = false
                        this.alert.class = 'alert alert-success sticky-top'
                        this.alert.message = 'Profile updated'
                        this.alert.show = true
                    } else {
                        this.ifMessage = response.data.message;
                        this.showUpdateError(this.ifMessage)
                    }
                }).catch(function (error) {
                    this.ifMessage = error.response.data.message;
                    if (Array.isArray(error.response.data.data.errors) === true && error.response.data.data.errors.length > 0) {
                        this.ifMessage = ''
                        for (let i = 0; i < error.response.data.data.errors.length; i++) {
                            this.ifMessage += error.response.data.data.errors[i] + '; '
                        }
                        this.showUpdateError(this.ifMessage)
                    }
                }.bind(this));
            }
        },

        hideAlert() {
            this.alert.show = false
        },
    },

    computed: {
        changePasswordStatus() {
            if (this.password.old.length > 0 && this.password.new.length > 0) {
                return 'active'
            } else {
                return 'disabled'
            }
        },
        statusBadge() {
            if (this.user.status === 'active') {
                return 'badge text-bg-success'
            } else if (this.user.status === 'created') {
                return 'badge text-bg-primary'
            } else if (this.user.status === 'deleted') {
                return 'badge text-bg-danger'
            } else {
                return 'badge text-bg-warning'
            }
        },
        roleBadge() {
            if (this.user.role === 'admin') {
                return 'badge text-bg-danger'
            } else if (this.user.role === 'moderator') {
                return 'badge text-bg-warning'
            } else if (this.user.role === 'user') {
                return 'badge text-bg-success'
            } else {
                return 'badge text-bg-primary'
            }
        },
        ratingBadge() {
            if (this.user.rating >= 0 && this.user.rating < 10) {
                return 'badge text-bg-danger'
            } else if (this.user.rating >= 10 && this.user.rating < 20) {
                return 'badge text-bg-warning'
            } else if (this.user.rating >= 20 && this.user.rating < 30) {
                return 'badge text-bg-success'
            } else {
                return 'badge text-bg-primary'
            }
        },
        emailApprovedBadge() {
            if (this.user.email_approved === true) {
                return 'badge text-bg-success'
            } else {
                return 'badge text-bg-danger'
            }
        },
        phoneApprovedBadge() {
            if (this.user.phone_approved === true) {
                return 'badge text-bg-success'
            } else {
                return 'badge text-bg-danger'
            }
        },
    },

    mounted() {
        const startData = JSON.parse(document.getElementById('start-data').innerHTML)
        //startData.user.languages = JSON.stringify(startData.user.languages)

        this.user = startData.user
        this.languages = startData.languages
        this.sexes = startData.sexes
        this.contacts = startData.contacts

        console.log('Vue profile loaded...')
    }
}