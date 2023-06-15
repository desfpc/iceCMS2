<?php
declare(strict_types=1);

use iceCMS2\Controller\AbstractController;
use iceCMS2\Models\User;

/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Authorization template
 *
 * @var AbstractController $this
 *
 */

/** @var User $user */
$user = $this->templateData['user'];



?>
<div class="container">
    <div class="row">
        <div class="col">
            <?php include($this->_getLayoutPath() . '_alerts.php'); ?>
            <h1><?= $user->get('nikname'); ?> profile</h1>
            <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
            <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

            <div id="app">
                <div v-if="alert.show" :class="alert.class" role="alert">
                    {{ alert.message }} <button type="button" class="btn-close float-end" aria-label="Close" @click="hideAlert()"></button>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <span class="me-3">Status: <span :class="statusBadge">{{ user.status }}</span></span>
                        <span class="me-3">Role: <span :class="roleBadge">{{ user.role }}</span></span>
                        <span class="me-3">Rating: <span :class="ratingBadge">{{ user.rating }}</span></span>
                        <span class="me-3">Email approved: <span :class="emailApprovedBadge"><?=
                                $user->get('email_approved') ? 'true' : 'false'; ?></span></span>
                        <span class="me-3">Phone approved: <span :class="phoneApprovedBadge"><?=
                                $user->get('phone_approved') ? 'true' : 'false'; ?></span></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-lg-3 text-center">
                        <img :src="user.avatar" class="img-thumbnail user_avatar">
                        <br><input type="file" class="form-control" id="file" ref="file" @change="onUploadFiles" />
                    </div>
                    <div class="col-sm-9">
                        <div class="mb-3 row">
                            <label for="name" class="col-sm-1 col-form-label text-end">Sex: </label>
                            <div class="col-sm-5">
                                <select class="form-control" v-model="user.sex">
                                    <option v-for="sex in sexes" :value="sex.value">{{ sex.text }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="staticEmail" class="col-sm-1 col-form-label text-end">Email: </label>
                            <div class="col-sm-5">
                                <input type="text" readonly class="form-control" id="staticEmail" value="<?=
                                $user->get('email'); ?>">
                            </div>
                            <label for="name" class="col-sm-1 col-form-label text-end">Name: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="name" v-model="user.name">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="telegram" class="col-sm-1 col-form-label text-end">Telegram: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="telegram" v-model="user.telegram">
                            </div>
                            <label for="nikname" class="col-sm-1 col-form-label text-end">Nickname: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="nikname" v-model="user.nikname">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="phone" class="col-sm-1 col-form-label text-end">Phone: </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="phone" v-model="user.phone">
                            </div>
                            <label for="language" class="col-sm-1 col-form-label text-end">Language: </label>
                            <div class="col-sm-5">
                                <select class="form-control" v-model="user.language">
                                    <option v-for="language in languages" :value="language.value">{{ language.text }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3 pt-3 row">
                    <div class="col-sm-12 col-form-label"><h5>Additional Contacts: </h5></div>
                </div>
                <div class="row mb-3" v-for="contact in contacts">
                    <label class="col-sm-1 col-form-label">{{ contact }} </label>
                    <div class="col-sm-11">
                        <input type="text" class="form-control" v-model="user.contacts[contact]">
                    </div>
                </div>
                <div class="mb-3 pt-3 row">
                    <div class="col-sm-1">
                        <button type="button" class="btn btn-primary" @click="save">Save</button>
                    </div>
                </div>
            </div>

            <script>
                const { createApp } = Vue

                createApp({
                    data() {
                        return {
                            user: {
                                phone: '<?= $user->get('phone'); ?>',
                                telegram: '<?= $user->get('telegram'); ?>',
                                language: '<?= $user->get('language'); ?>',
                                name: '<?= $user->get('name'); ?>',
                                nikname: '<?= $user->get('nikname'); ?>',
                                status: '<?= $user->get('status'); ?>',
                                role: '<?= $user->get('role'); ?>',
                                rating: '<?= $user->get('rating'); ?>',
                                avatar: '<?= $user->avatarUrl; ?>',
                                created_time: '<?= $user->get('created_time'); ?>',
                                sex: '<?= $user->get('sex'); ?>',
                                contacts: <?= is_null($user->get('contacts')) ? '{}' : $user->get('contacts'); ?>,
                            },
                            alert: {
                                show: false,
                                class: 'alert',
                                message: '',
                            },
                            languages: [
                                { text: 'English', value: 'en' },
                                { text: 'Русский', value: 'ru' },
                                { text: 'ქართული', value: 'ge' },
                            ],
                            sexes: [
                                { text: 'Male', value: 'male' },
                                { text: 'Female', value: 'female' },
                                { text: 'Other', value: 'other' },
                            ],
                            contacts: [ 'Country', 'City', 'Address', 'Zip', 'Twitter', 'Instagram', 'LinkedIn',
                                'YouTube', 'Discord', 'Website', 'Blog', 'Other' ],
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

                        save() {
                            axios.post('/api/v1/profile/update', this.user).then(response => {

                                if (response.data.success === true) {
                                    this.alert.class = 'alert alert-success sticky-top'
                                    this.alert.message = 'Profile updated'
                                    this.alert.show = true
                                } else {
                                    this.alert.class = 'alert alert-danger sticky-top'
                                    this.alert.message = 'Error in profile update'
                                    this.alert.show = true
                                }

                            })
                        },

                        hideAlert() {
                            this.alert.show = false
                        },
                    },

                    computed: {
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
                    }
                }).mount('#app')
            </script>
            <p>&nbsp;</p>
        </div>
    </div>
</div>