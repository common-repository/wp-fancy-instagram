<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div><h2>插件设置</h2><br>
    <form method="post" action="options.php">
        <?php
        settings_fields( 'wfi_setting_group' );
        $setting = wfi_get_setting();
        ?>
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row"><label>使用方法</label></th>
                <td><p>方法1：</p>
                    <p>新建一个页面：文本框输入 <code>[wfi][/wfi]</code> 即可</p>
                    <p>方法2：</p>
                    <p>新建一个模板，使用下面的函数：</p>
                    <p>添加 <code>&lt;?php wp_fancy_instagram();?&gt;</code> 到需要的位置</p>
                    <p>方法3：</p>
                    <p>如果您使用的是Puma 主题，则新建页面，选择Instagram 模板即可。</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label>插件设置</label></th>
                <td>
                    <ul class="wfi-color-ul">
                        <?php $color = array(
                            array(
                                'title' => '帐号token',
                                'key' => 'token',
                                'default' => ''
                            ),
                            array(
                                'title' => '每页显示数量',
                                'key' => 'number',
                                'default' => '20'
                            ),
                        );
                        foreach ($color as $key => $V) {
                            ?>
                            <li class="wfi-color-li">
                                <code><?php echo $V['title'];?></code>
                                <?php $color = $setting[$V['key']] ? $setting[$V['key']] : $V['default'];?>
                                <input name="<?php echo wfi_setting_key($V['key']);?>" type="text" value="<?php echo $color;?>" class="regular-text wfi-color-picker" />
                            </li>
                        <?php }
                        ?>
                        <li class="wfi-color-li">
                                <code>显示点赞照片</code>
                                <input name="wfi_setting[liked]" type="checkbox" value="1" <?php if( $setting['liked'] ) echo 'checked=true';?>/>
                            </li>
                    </ul>
                    <p class="description">请使用浏览器打开<a href="https://dev.fatesinger.com/get-instagram-token" target="_blank">https://dev.fatesinger.com/get-instagram-token</a>,点击获取token,授权后会显示token，复制下来填入保存即可。</p>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="wfi-submit-form">
            <input type="submit" class="button-primary muhermit_submit_form_btn" name="save" value="<?php _e('Save Changes') ?>"/>
        </div>
    </form>
    <style>
        .wfi-color-li{position: relative;padding-left: 120px}
        .wfi-color-li code{position: absolute;left: 0;top: 1px;}
    </style>
</div>