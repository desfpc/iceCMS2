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

//TODO js arrays enums + contacts - create DB catalog tables and load it

?>
<div class="container">
    <div class="row">
        <div class="col">
            <?php include($this->_getLayoutPath() . '_alerts.php'); ?>
            <h1><?= $user->get('nikname'); ?> profile</h1>
            <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
            <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
            <div id="start-data" style="display: none;">
                {
                    "user": {
                        "phone": "<?= $user->get('phone'); ?>",
                        "telegram": "<?= $user->get('telegram'); ?>",
                        "language": "<?= $user->get('language'); ?>",
                        "languages": <?= empty($user->get('languages')) ? '[]' : $user->get('languages'); ?>,
                        "name": "<?= $user->get('name'); ?>",
                        "nikname": "<?= $user->get('nikname'); ?>",
                        "status": "<?= $user->get('status'); ?>",
                        "role": "<?= $user->get('role'); ?>",
                        "rating": "<?= $user->get('rating'); ?>",
                        "avatar": "<?= $user->avatarUrl; ?>",
                        "created_time": "<?= $user->get('created_time'); ?>",
                        "sex": "<?= $user->get('sex'); ?>",
                        "contacts": <?= is_null($user->get('contacts')) ? "{}" : $user->get('contacts'); ?>
                    },
                    "languages": [
                        { "text": "English", "value": "en" },
                        { "text": "Русский", "value": "ru" },
                        { "text": "ქართული", "value": "ge" },
                        { "text": "Српски", "value": "rs" }
                    ],
                    "sexes": [
                        { "text": "Male", "value": "male" },
                        { "text": "Female", "value": "female" },
                        { "text": "Other", "value": "other" }
                    ],
                    "contacts": [ "Country", "City", "Address", "Zip", "Twitter", "Instagram", "LinkedIn", "YouTube", "Discord", "Website", "Blog", "Other"]
                }
            </div>

            <div id="app">
                <div v-if="alert.show" :class="alert.class" role="alert">
                    {{ alert.message }} <button type="button" class="btn-close float-end" aria-label="Close" @click="hideAlert()"></button>
                </div>

                <Tabs tabsjson='[{"name": "Profile", "id": "tab_profile"},
                {"name": "Social connections", "id": "tab_social"}]' startactivetab="tab_profile">
                    <template v-slot:tab_profile>
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
                                        <Validate v-slot="{ errorClass }" :rule="$validationRules.string" :value="user.sex" :func="$globalValidation">
                                            <select class="form-control" :class="errorClass" v-model="user.sex">
                                                <option v-for="sex in sexes" :value="sex.value">{{ sex.text }}</option>
                                            </select>
                                        </Validate>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="staticEmail" class="col-sm-1 col-form-label text-end">Email: </label>
                                    <div class="col-sm-5">
                                        <input type="text" readonly disabled class="form-control" id="staticEmail" value="<?=
                                        $user->get('email'); ?>">
                                    </div>
                                    <label for="name" class="col-sm-1 col-form-label text-end">Name: </label>
                                    <div class="col-sm-5">
                                        <Validate v-slot="{ errorClass }" :rule="$validationRules.empty" :value="user.name" :func="$globalValidation">
                                            <input type="text" class="form-control" :class="errorClass" id="name" v-model="user.name">
                                        </Validate>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="telegram" class="col-sm-1 col-form-label text-end">Telegram: </label>
                                    <div class="col-sm-5">
                                        <Validate v-slot="{ errorClass }" :rule="$validationRules.empty" :value="user.telegram" :func="$globalValidation">
                                            <input type="text" class="form-control" :class="errorClass" id="telegram" v-model="user.telegram">
                                        </Validate>
                                    </div>
                                    <label for="nikname" class="col-sm-1 col-form-label text-end">Nickname: </label>
                                    <div class="col-sm-5">
                                        <Validate v-slot="{ errorClass }" :rule="$validationRules.string" :value="user.nikname" :func="$globalValidation">
                                            <input type="text" class="form-control" :class="errorClass" id="nikname" v-model="user.nikname">
                                        </Validate>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="telegram" class="col-sm-2 col-form-label text-end">Language: </label>
                                    <div class="col-sm-4">
                                        <Validate v-slot="{ errorClass }" :rule="$validationRules.string" :value="user.language" :func="$globalValidation">
                                            <select class="form-control" :class="errorClass" v-model="user.language">
                                                <option v-for="language in languages" :value="language.value">{{ language.text }}</option>
                                            </select>
                                        </Validate>
                                    </div>
                                    <label for="languages" class="col-sm-2 col-form-label text-end">Known languages: </label>
                                    <div class="col-sm-4">
                                        <Checkbox-Group :value="user.languages" :data-Array="languages" :callback="setLanguages" data-Key="languages"></Checkbox-Group>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <hr style="padding: 0 10px; background: #dddddd; width: 100%; height: 1px; border: 0;" />
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <label for="oldPassword" class="col-sm-12 col-form-label">Old password: </label>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <input type="password" class="form-control" id="oldPassword" v-model="password.old">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <label for="newPassword" class="col-sm-12 col-form-label">New password: </label>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <input type="password" class="form-control" id="newPasswordnn" v-model="password.new">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col-sm">
                                        <input type="button" class="btn btn-danger" :class="changePasswordStatus" @click="changePassword" value="Change password">
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
                    </template>
                    <template v-slot:tab_social>
                        Hard working for this!
                    </template>
                </Tabs>

            </div>

            <script type="module" src="/js/vendor/authorize/profile-app.js"></script>

            <p>&nbsp;</p>
        </div>
    </div>
</div>