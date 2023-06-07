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
            <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

            <div id="app">
                <div class="mb-3 row">
                    <label for="staticEmail" class="col-sm-1 col-form-label text-end">Email: </label>
                    <div class="col-sm-5">
                        <input type="text" readonly class="form-control" id="staticEmail" value="<?=
                        $user->get('email'); ?>">
                    </div>
                    <label for="phone" class="col-sm-1 col-form-label text-end">Phone: </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="phone" v-model="user.phone">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="telegram" class="col-sm-1 col-form-label text-end">Telegram: </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="telegram" v-model="user.telegram">
                    </div>
                    <label for="language" class="col-sm-1 col-form-label text-end">Language: </label>
                    <div class="col-sm-5">
                        <select class="form-control" v-model="user.language">
                            <option v-for="language in languages" :value="language.value">{{ language.text }}</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    
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
                                avatar: '<?= $user->get('avatar'); ?>',
                                created_time: '<?= $user->get('created_time'); ?>',
                                sex: '<?= $user->get('sex'); ?>',
                                contacts: '<?= $user->get('contacts'); ?>',
                            },
                            languages: [
                                { text: 'English', value: 'en' },
                                { text: 'Русский', value: 'ru' },
                                { text: 'ქართული', value: 'ge' },
                            ]
                        }
                    }
                }).mount('#app')
            </script>
            <p>&nbsp;</p>
        </div>
    </div>
</div>