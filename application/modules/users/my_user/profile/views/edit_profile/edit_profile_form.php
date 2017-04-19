<!-- /.box-header -->
<div class="box-body">

    <a class="anchor" id="Profile"></a>
    <h3>Personal info</h3>

    <form class="form-horizontal" action="<?= base_url('/profile/edit/#Profile');?>" role="form" method="post">

        <?php
            $this->AlertsContainer->renderViewByName('g_msgEditProfileSuccess');
            $this->AlertsContainer->renderViewByName('g_msgEditProfileError');
        ?>

        <div class="form-group">
            <label class="col-lg-3 control-label">First name:</label>
            <div class="col-lg-8">
                <div style="margin-bottom: 25px" class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-font"></i></span>
                    <input class="form-control" name="editProfile-firstName" type="text" value="<?=isset($_POST['editProfile-firstName'])?$_POST['editProfile-firstName']:$g_User->sFirstName?>" placeholder="first name" required>
                </div>

            </div>
        </div>


        <div class="form-group">
            <label class="col-lg-3 control-label">Last name:</label>
            <div class="col-lg-8">
                <div style="margin-bottom: 25px" class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-font"></i></span>
                    <input class="form-control" type="text" name="editProfile-lastName" value="<?=isset($_POST['editProfile-lastName'])?$_POST['editProfile-lastName']:$g_User->sLastName?>" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label">Bio:</label>
            <div class="col-lg-8">

                <div style="margin-bottom: 25px" class="input-group">
                    <span class="input-group-addon"><i class="fa fa-edit"></i></span>
                    <textarea id="editProfile-bio" name="editProfile-bio" class="form-control" rows="5" ><?=isset($_POST['editProfile-bio'])?$_POST['editProfile-bio']:$g_User->sBiography?></textarea>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">Company:</label>
            <div class="col-lg-8">
                <div style="margin-bottom: 25px" class="input-group">
                    <span class="input-group-addon"><i class="fa fa-institution"></i></span>
                    <input class="form-control" name="editProfile-company"  type="text" value="<?=isset($_POST['editProfile-company'])?$_POST['editProfile-company']:$g_User->sCompany?>">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">Website:</label>
            <div class="col-lg-8">
                <div style="margin-bottom: 25px" class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                    <input class="form-control" name="editProfile-webSite"  type="text" value="<?=isset($_POST['editProfile-webSite'])?$_POST['editProfile-webSite']:$g_User->sWebsite?>">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">Time Zone:</label>
            <div class="col-lg-8">
                <div class="ui-select">
                    <div style="margin-bottom: 25px" class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                        <select id="user_time_zone" class="form-control" name="editProfile-timeZone" >
                            <option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
                            <option value="America/Adak">(GMT-10:00) Hawaii-Aleutian</option>
                            <option value="Etc/GMT+10">(GMT-10:00) Hawaii</option>
                            <option value="Pacific/Marquesas">(GMT-09:30) Marquesas Islands</option>
                            <option value="Pacific/Gambier">(GMT-09:00) Gambier Islands</option>
                            <option value="America/Anchorage">(GMT-09:00) Alaska</option>
                            <option value="America/Ensenada">(GMT-08:00) Tijuana, Baja California</option>
                            <option value="Etc/GMT+8">(GMT-08:00) Pitcairn Islands</option>
                            <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
                            <option value="America/Denver">(GMT-07:00) Mountain Time (US & Canada)</option>
                            <option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
                            <option value="America/Dawson_Creek">(GMT-07:00) Arizona</option>
                            <option value="America/Belize">(GMT-06:00) Saskatchewan, Central America</option>
                            <option value="America/Cancun">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
                            <option value="Chile/EasterIsland">(GMT-06:00) Easter Island</option>
                            <option value="America/Chicago">(GMT-06:00) Central Time (US & Canada)</option>
                            <option value="America/New_York">(GMT-05:00) Eastern Time (US & Canada)</option>
                            <option value="America/Havana">(GMT-05:00) Cuba</option>
                            <option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
                            <option value="America/Caracas">(GMT-04:30) Caracas</option>
                            <option value="America/Santiago">(GMT-04:00) Santiago</option>
                            <option value="America/La_Paz">(GMT-04:00) La Paz</option>
                            <option value="Atlantic/Stanley">(GMT-04:00) Faukland Islands</option>
                            <option value="America/Campo_Grande">(GMT-04:00) Brazil</option>
                            <option value="America/Goose_Bay">(GMT-04:00) Atlantic Time (Goose Bay)</option>
                            <option value="America/Glace_Bay">(GMT-04:00) Atlantic Time (Canada)</option>
                            <option value="America/St_Johns">(GMT-03:30) Newfoundland</option>
                            <option value="America/Araguaina">(GMT-03:00) UTC-3</option>
                            <option value="America/Montevideo">(GMT-03:00) Montevideo</option>
                            <option value="America/Miquelon">(GMT-03:00) Miquelon, St. Pierre</option>
                            <option value="America/Godthab">(GMT-03:00) Greenland</option>
                            <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
                            <option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
                            <option value="America/Noronha">(GMT-02:00) Mid-Atlantic</option>
                            <option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
                            <option value="Atlantic/Azores">(GMT-01:00) Azores</option>
                            <option value="Europe/Belfast">(GMT) Greenwich Mean Time : Belfast</option>
                            <option value="Europe/Dublin">(GMT) Greenwich Mean Time : Dublin</option>
                            <option value="Europe/Lisbon">(GMT) Greenwich Mean Time : Lisbon</option>
                            <option value="Europe/London">(GMT) Greenwich Mean Time : London</option>
                            <option value="Africa/Abidjan">(GMT) Monrovia, Reykjavik</option>
                            <option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
                            <option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
                            <option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
                            <option value="Africa/Algiers">(GMT+01:00) West Central Africa</option>
                            <option value="Africa/Windhoek">(GMT+01:00) Windhoek</option>
                            <option value="Asia/Beirut">(GMT+02:00) Beirut</option>
                            <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
                            <option value="Asia/Gaza">(GMT+02:00) Gaza</option>
                            <option value="Africa/Blantyre">(GMT+02:00) Harare, Pretoria</option>
                            <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
                            <option value="Europe/Minsk">(GMT+02:00) Minsk</option>
                            <option value="Asia/Damascus">(GMT+02:00) Syria</option>
                            <option value="Europe/Moscow">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
                            <option value="Africa/Addis_Ababa">(GMT+03:00) Nairobi</option>
                            <option value="Asia/Tehran">(GMT+03:30) Tehran</option>
                            <option value="Asia/Dubai">(GMT+04:00) Abu Dhabi, Muscat</option>
                            <option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
                            <option value="Asia/Kabul">(GMT+04:30) Kabul</option>
                            <option value="Asia/Yekaterinburg">(GMT+05:00) Ekaterinburg</option>
                            <option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
                            <option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
                            <option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
                            <option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
                            <option value="Asia/Novosibirsk">(GMT+06:00) Novosibirsk</option>
                            <option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
                            <option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
                            <option value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk</option>
                            <option value="Asia/Hong_Kong">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
                            <option value="Asia/Irkutsk">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
                            <option value="Australia/Perth">(GMT+08:00) Perth</option>
                            <option value="Australia/Eucla">(GMT+08:45) Eucla</option>
                            <option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
                            <option value="Asia/Seoul">(GMT+09:00) Seoul</option>
                            <option value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option>
                            <option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
                            <option value="Australia/Darwin">(GMT+09:30) Darwin</option>
                            <option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
                            <option value="Australia/Hobart">(GMT+10:00) Hobart</option>
                            <option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option>
                            <option value="Australia/Lord_Howe">(GMT+10:30) Lord Howe Island</option>
                            <option value="Etc/GMT-11">(GMT+11:00) Solomon Is., New Caledonia</option>
                            <option value="Asia/Magadan">(GMT+11:00) Magadan</option>
                            <option value="Pacific/Norfolk">(GMT+11:30) Norfolk Island</option>
                            <option value="Asia/Anadyr">(GMT+12:00) Anadyr, Kamchatka</option>
                            <option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
                            <option value="Etc/GMT-12">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
                            <option value="Pacific/Chatham">(GMT+12:45) Chatham Islands</option>
                            <option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option>
                            <option value="Pacific/Kiritimati">(GMT+14:00) Kiritimati</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label">Username:</label>
            <div class="col-md-8">
                <div style="margin-bottom: 25px" class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                    <input class="form-control" type="text" name="editProfile-username" value="<?=$g_User->sUserName?>"disabled>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-7" style="padding-bottom: 10px;">
                <div class="form-item" style="text-align: left; ">
                    <input id="country_selector" type="text" style="width: 100%;" >
                    <label for="country_selector" style="display:none;">Select a country here...</label>
                </div>
                <div class="form-item" style="display:none; text-align: left;">
                    <input type="text" id="country_selector_code" name="editProfile-country" data-countrycodeinput="1"   readonly="readonly" placeholder="Selected country code will appear here" />
                    <label for="country_selector_code">...and the selected country code will be updated here</label>
                </div>
            </div>
            <div class="col-sm-5" style="padding-bottom: 10px;">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-institution"></i></span>
                    <input id="editProfileCity" type="text" class="form-control" name="editProfile-city" placeholder="city" value="<?=isset($_POST['editProfile-city'])?$_POST['editProfile-city']:$g_User->sCity?>"
                           data-validation="length alpha"
                           data-validation-length="2-18"
                           data-validation-error-msg="City has to be an alphanumeric value (2-18 chars)" required >
                </div>
            </div>
        </div>

        <input type="hidden" name="val" value="edit_profile">

        <div class="form-group">
            <label class="col-md-3 control-label"></label>
            <div class="col-md-8">
                <input type="submit" class="btn btn-primary" value="Save Changes">
                <span></span>
                <input type="reset" class="btn btn-warning" ">
            </div>
        </div>
    </form>
</div>


<?php $this->BottomScriptsContainer->addScript("<script>
    $('#country_selector').countrySelect({
        ". ((isset($_POST['editProfile-country'])) ? "defaultCountry: '".$_POST['editProfile-country']."',":$g_User->sCountry).
    //onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
    "preferredCountries: ['ca', 'gb', 'us'".((isset($_POST['editProfile-country'])&&(!in_array($_POST['editProfile-country'],['ca', 'gb', 'us']))) ? ",'".$_POST['editProfile-country']."'":'')."]
    });
</script>");?>