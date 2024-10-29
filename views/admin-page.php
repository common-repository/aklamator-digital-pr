
<div id="aklamator-options" style="width:1160px;margin-top:10px;">
    <div class="left" style="float: left">

        <div style="float: left; width: 300px;">

            <a target="_blank" href="<?php echo $this->aklamator_url; ?>?utm_source=wp-plugin">
                <img style="border-radius:5px;border:0px;"
                     src=" <?php echo AKLA_PR_PLUGIN_URL.'images/logo.jpg'; ?>"/></a>
            <?php
            if ($this->application_id != '') : ?>
                <a target="_blank" href="<?php echo $this->aklamator_url; ?>dashboard?utm_source=wp-plugin">
                    <img style="border:0px;margin-top:5px;border-radius:5px;"
                         src="<?php echo AKLA_PR_PLUGIN_URL.'images/dashboard.jpg'; ?>"/></a>

            <?php endif; ?>

            <a target="_blank" href="<?php echo $this->aklamator_url; ?>contact?utm_source=wp-plugin-contact">
                <img style="border:0px;margin-top:5px; margin-bottom:5px;border-radius:5px;"
                     src="<?php echo AKLA_PR_PLUGIN_URL.'images/support.jpg'; ?>"/></a>

            <a target="_blank" href="http://qr.rs/q/4649f"><img
                    style="border:0px;margin-top:5px; margin-bottom:5px;border-radius:5px;"
                    src="<?php echo AKLA_PR_PLUGIN_URL.'images/promo-300x200.png'; ?>"/></a>

        </div>

        <div class="box">

            <h1>Aklamator Digital PR</h1>

            <?php

            if (isset($this->api_data->error) || $this->application_id == '') : ?>
                <h3 style="float: left">Step 1: Get your Aklamator Aplication ID</h3>
                <a class='aklamator_button aklamator-login-button' id="aklamator_login_button" >Click here for FREE registration/login</a>
                <div style="clear: both"></div>
                <p>Or you can manually <a href="<?php echo $this->aklamator_url . 'registration/publisher'; ?>" target="_blank">register</a> or <a href="<?php echo $this->aklamator_url . 'login'; ?>" target="_blank">login</a> and copy paste your Application ID</p>
                <script>var signup_url = '<?php echo $this->getSignupUrl(); ?>';</script>
            <?php endif; ?>


            <div style="clear: both"></div>
            <?php if ($this->application_id == '') { ?>
                <h3>Step 2: &nbsp;&nbsp;&nbsp;&nbsp; Paste your Aklamator Application ID</h3>
            <?php } else { ?>
                <h3>Your Aklamator Application ID</h3>
            <?php } ?>


            <form method="post" action="options.php">
                <?php
                settings_fields('aklamator-options');
                ?>

                <p>
                    <input type="text" style="width: 400px" name="aklamatorApplicationID"
                           id="aklamatorApplicationID" value="<?php
                    echo(get_option("aklamatorApplicationID"));
                    ?>" maxlength="999" onchange="appIDChange(this.value)"/>

                </p>
                <p>
                    <input type="checkbox" id="aklamatorPoweredBy"
                           name="aklamatorPoweredBy" <?php echo(get_option("aklamatorPoweredBy") == true ? 'checked="checked"' : ''); ?>
                           Required="Required">
                    <strong>Required</strong> I acknowledge there is a 'powered by aklamator' link on the widget.
                    <br/>
                </p>
                <p>
                    <input type="checkbox" id="aklamatorFeatured2Feed"
                           name="aklamatorFeatured2Feed" <?php echo(get_option("aklamatorFeatured2Feed") == true ? 'checked="checked"' : ''); ?> >
                    <strong>Add featured</strong> images from posts to your site's RSS feed output
                </p>

                <p>
                <div class="alert alert-msg">
                    <strong>Note </strong><span style="color: red">*</span>: By default, posts without images will
                    not be shown in widgets. If you want to show them click on <strong>EDIT</strong> in table below!
                </div>
                </p>
                <?php if(isset($this->api_data->flag) && $this->api_data->flag === false): ?>
                    <p id="aklamator_error" class="alert_red alert-msg_red"><span style="color:red"><?php echo $this->api_data->error; ?></span></p>
                <?php endif; ?>

                <?php if ($this->application_id !== '' && $this->api_data->flag): ?>

                    <p>
                    <h1>Options</h1>

                    <h4>Show all categories in widget (default), or choose specific category:</h4>
                    <?php

                    wp_dropdown_categories(array(
                        'id' => 'aklamatorCategory',
                        'name' => 'aklamatorCategory',
                        'hide_empty' => 1,
                        'orderby' => 'name',
                        'selected' => get_option('aklamatorCategory'),
                        'hierarchical' => true,
                        'show_option_none' => __('ALL'),
                        'taxonomy' => 'category',
                        'value_field' => 'term_id',
                        'show_count' => true
                    ));
                    ?>

                    <h4>Select widget to be shown on bottom of the each:</h4>

                    <label for="aklamatorSingleWidgetTitle">Title Above widget (Optional): </label>
                    <input type="text" style="width: 300px; margin-bottom:10px" name="aklamatorSingleWidgetTitle"
                           id="aklamatorSingleWidgetTitle"
                           value="<?php echo(get_option("aklamatorSingleWidgetTitle")); ?>" maxlength="999"/>

                    <?php

                    $widgets = $this->api_data->data;

                    /* Add new item to the end of array */
                    $item_add = new stdClass();
                    $item_add->uniq_name = 'none';
                    $item_add->title = 'Do not show';
                    $widgets[] = $item_add;

                    ?>

                    <label for="aklamatorSingleWidgetID">Single post: </label>
                    <select id="aklamatorSingleWidgetID" name="aklamatorSingleWidgetID">
                        <?php
                        foreach ($widgets as $item): ?>
                            <option <?php echo (get_option('aklamatorSingleWidgetID') == $item->uniq_name) ? 'selected="selected"' : ''; ?>
                                value="<?php echo $item->uniq_name; ?>"><?php echo $item->title; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input style="margin-left: 5px;" id="preview_single" type="button"
                           class="button primary big submit"
                           onclick="myFunction(jQuery('#aklamatorSingleWidgetID option[selected]').val())"
                           value="Preview" <?php echo get_option('aklamatorSingleWidgetID') == "none" ? "disabled" : ""; ?>>
                    </p>

                    <p>
                        <label for="aklamatorPageWidgetID">Single page: </label>
                        <select id="aklamatorPageWidgetID" name="aklamatorPageWidgetID">
                            <?php
                            foreach ($widgets as $item): ?>
                                <option <?php echo (get_option('aklamatorPageWidgetID') == $item->uniq_name) ? 'selected="selected"' : ''; ?>
                                    value="<?php echo $item->uniq_name; ?>"><?php echo $item->title; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input style="margin-left: 5px;" type="button" id="preview_page"
                               class="button primary big submit"
                               onclick="myFunction(jQuery('#aklamatorPageWidgetID option[selected]').val())"
                               value="Preview" <?php echo get_option('aklamatorPageWidgetID') == "none" ? "disabled" : ""; ?>>

                    </p>


                <?php endif; ?>

                <input id="aklamator_pr_save" class="aklamator_INlogin" style ="margin: 0; border: 0; float: left;" type="submit" value="<?php echo (_e("Save Changes")); ?>" />
                <?php if(!isset($this->api_data->flag) || !$this->api_data->flag): ?>
                    <div style="float: left; padding: 7px 0 0 10px; color: red; font-weight: bold; font-size: 16px"> <-- In order to proceed save changes</div>
                <?php endif ?>

            </form>
        </div>


        <div style="clear:both"></div>
        <div style="margin-top: 20px; margin-left: 0px; width: 810px;" class="box">

            <?php if (isset($this->curlfailovao) && $this->curlfailovao && $this->application_id != ''): ?>
                <h2 style="color:red">Error communicating with Aklamator server, please refresh plugin page or try again
                    later. </h2>
            <?php endif; ?>
            <?php if (!isset($this->api_data->flag) || !$this->api_data->flag): ?>
                <a href="<?php echo $this->getSignupUrl(); ?>" target="_blank"><img
                        style="border-radius:5px;border:0px;"
                        src=" <?php echo AKLA_PR_PLUGIN_URL.'images/teaser-810x262.png'; ?>"/></a>
            <?php else : ?>
            <!-- Start of dataTables -->
            <div id="aklamatorPro-options">
                <h1>Your Widgets</h1>
                <div>In order to add new widgets or change dimensions please <a
                        href="<?php echo $this->aklamator_url; ?>login" target="_blank">login to aklamator</a></div>
            </div>
            <br>
            <table cellpadding="0" cellspacing="0" border="0"
                   class="responsive dynamicTable display table table-bordered" width="100%">
                <thead>
                <tr>

                    <th>Name</th>
                    <th>Domain</th>
                    <th>Settings</th>
                    <th>Image size</th>
                    <th>Column/row</th>
                    <th>Created At</th>

                </tr>
                </thead>
                <tbody>
                <?php foreach ($this->api_data->data as $item): ?>

                    <tr class="odd">
                        <td style="vertical-align: middle;"><?php echo $item->title; ?></td>
                        <td style="vertical-align: middle;">
                            <?php foreach ($item->domain_ids as $domain): ?>
                                <a href="<?php echo $domain->url; ?>" target="_blank"><?php echo $domain->title; ?></a>
                                <br/>
                            <?php endforeach; ?>
                        </td>
                        <td style="vertical-align: middle">
                            <div style="float: left; margin-right: 10px" class="button-group">
                                <input type="button" class="button primary big submit"
                                       onclick="myFunction('<?php echo $item->uniq_name; ?>')" value="Preview Widget">
                            </div>
                        </td>
                        <td style="vertical-align: middle;"><?php echo "<a href = \"$this->aklamator_url" . "widget/edit/$item->id\" target='_blank' title='Click & Login to change'>$item->img_size px</a>"; ?></td>
                        <td style="vertical-align: middle;"><?php echo "<a href = \"$this->aklamator_url" . "widget/edit/$item->id\" target='_blank' title='Click & Login to change'>" . $item->column_number . " x " . $item->row_number . "</a>"; ?>

                            <div style="float: right;">
                                <?php echo "<a class=\"btn btn-primary\" href = \"$this->aklamator_url" . "widget/edit/$item->id\" target='_blank' title='Edit widget settings'>Edit</a>"; ?>
                            </div>

                        </td>
                        <td style="vertical-align: middle;"><?php echo $item->date_created; ?></td>


                    </tr>
                <?php endforeach; ?>

                </tbody>
                <tfoot>
                <tr>
                    <th>Name</th>
                    <th>Domain</th>
                    <th>Settings</th>
                    <th>Immg size</th>
                    <th>Column/row</th>
                    <th>Created At</th>
                </tr>
                </tfoot>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <div class="right" style="float: right">
        <!-- right sidebar -->
        <div class="right_sidebar">
            <iframe width="330" height="1024" src="<?php echo $this->aklamator_url; ?>wp-sidebar/right?plugin=digital-pr" frameborder="0"></iframe>
        </div>
        <!-- End Right sidebar -->
    </div>
</div>

