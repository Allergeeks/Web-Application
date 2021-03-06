var loadUserPageJS = function () {
    showBlacklist();
    loadCategories();

    var intervallNewDevice;

    $("#button_blacklist").css("background-color", "#333");

    drawLeftHeader();

    $('#button_blacklist').click(function (e) {

        e.preventDefault();
        showBlacklist();
    });

    $('#button_profile').click(function (e) {

        e.preventDefault();

        showProfile();

    });

    $('#button_help').click(function(e) {
        $('div.help').toggle('slow');
    });

    $('#button_logout').click(function (e) {
        currentUser.logout().then(function (result) {
            if ( window.localStorage ) {
                window.localStorage.clear();
            }
            loadFrontPage();
        })
        .catch(function (err) {
                //console.log(err);
                //displayAlert('Fehler beim Logout!', 'error');
                loadFrontPage();
            }
        );
    });

    $('#button_filter').click(function (e) {

        e.preventDefault();

        $('.div_filter').toggle();
    });

    $('#button_new_device').click(function (e) {

        e.preventDefault();
        var oldNumberOfDevices;

        currentUser.getDevices().then(function (devices) {
            oldNumberOfDevices = devices.length;
        }).catch(function (err) {
            console.log(err);
            displayAlert('Fehler beim Laden der Verbindung!', 'error');
            return;
        });

        var bcType = "ean13";
        //"4060800123459";//"4002627400177";

        if ($('.div_new_device').css('display') == 'none') {
            clearInterval(intervallNewDevice);
            window.alert('Bitte starten Sie jetzt die Edible-App auf Ihrer Vuzix!');

            currentUser.getBarcode(currentUser.authToken)
                .then(function (barcode) {
                    barcode = barcode.toString();

                    intervallNewDevice = setInterval(function () {
                        currentUser.getDevices().then(function (devices) {
                            console.log(devices.length + ';' + oldNumberOfDevices);
                            if (devices.length > oldNumberOfDevices) {
                                clearInterval(intervallNewDevice);
                                $('.div_new_device').hide();
                                $('#div_barode').empty();
                                displayAlert('Das Gerät wurde erfolgreich gekoppelt!', 'success');

                                $('#div_device_table').empty();
                                var now = new Date();
                                var device;
                                currentUser.getDevices()
                                    .then(function (devices) {
                                        $('#div_device_table').append('<table id="table_devices"></table>');
                                        for (device = 0; device < devices.length; device++) {
                                            if ((devices[device].until * 1000) > now.getTime()) {
                                                if (devices[device].authToken != currentUser.authToken) {
                                                    $('#table_devices').append('<tr class="device"><th>' + devices[device].name + '</th><td><button id="' + devices[device].authToken + '" class="button_delete">X</button></td><tr>');
                                                    $('#' + devices[device].authToken).click(function (e) {
                                                        e.preventDefault();
                                                        $(this).parent().parent().remove();
                                                        User.deleteDevice(this.id);
                                                        display.Alert('Das Gerät wurde entfernt!', 'success');
                                                    });
                                                } else {
                                                    $('#table_devices').append('<tr class="device_current"><th>' + devices[device].name + '</th><td>Dieses Gerät</td><tr>');
                                                }
                                            }
                                        }
                                    })
                                    .catch(function (err) {
                                        console.log(err);
                                        displayAlert('Fehler beim Laden der Geräte!', 'error');
                                    });
                            }
                        }).catch(function (err) {
                            console.log(err);
                            displayAlert('Fehler beim Laden der Verbindung!', 'error');
                        });
                    }, 3000);

                    $('#div_barcode').barcode(barcode, bcType, {barWidth: 2, barHeight: 100});
                    $('.div_new_device').show();
                }).catch(function (err) {
                    console.log(err);
                    displayAlert('Fehler beim Laden der Verbindung!', 'error');
                });
        } else {
            displayAlert('Sie sind bereits dabei, ein neues Gerät zu koppeln', 'warning');
        }

    });

    $('#button_finished').click(function (e) {

        e.preventDefault();

        $('.div_new_device').hide();
        $('#div_barode').empty();
    });

    $('#button_cancel').click(function (e) {

        e.preventDefault();

        clearInterval(intervallNewDevice);
        $('.div_new_device').hide();
        $('#div_barode').empty();
    });

    $('#save_profile').click(function (e) {
        function handleErr( type ) {
            return function( err ) {
                var errmsg;
                switch(err.status) {
                    case 400:
                        errmsg = err.error;
                        break;
                    case 401:
                        errmsg = 'Das aktuelle Passwort ist nicht korrekt.';
                        break;
                    default:
                        errmsg = 'Fehler beim ändern der ' + type + '!';
                }
                displayAlert(errmsg, 'error');
            };
        }

        function handleSuccess( result ) {
            displayAlert('Daten wurden erfolgreich geändert!', 'success');
            drawLeftHeader();
        }

        e.preventDefault();

        var email = $('#email').val();
        var new_pw = $('#new').val();
        var confirm = $('#confirm').val();
        var old_pw = $('#old').val();

        if (email == "" && new_pw == "" && confirm == "") {
            displayAlert('Sie müssen erst Daten eingeben, um diese abspeichern zu können!', 'warning');
            return;
        } else if (old_pw == '') {
            displayAlert('Bitte geben Sie ihr altes Passwort an!', 'warning');
            return;
        } else if (new_pw != confirm) {
				if (email.indexOf("@") == -1){
					displayAlert('Bitte geben Sie eine korrekte Email-Adresse an!','warning');
					return;
				}
            displayAlert('Das neue Passwort und dessen Bestätigung müssen übereinstimmen!', 'warning');
        } else {
            if(email != "" && new_pw != "") {
                currentUser.changeAll( new_pw, email, old_pw)
                    .then(handleSuccess).catch(handleErr('Email und Passwort'));
            } else if (email != "") {
                currentUser.changeEmail(email, old_pw)
                    .then(handleSuccess).catch(handleErr('Email'));
            } else if (new_pw != "") {
                currentUser.changePassword(old_pw, new_pw)
                    .then(handleSuccess).catch(handleErr('Passwort'));
            }
        }
        $('#table_profile_conf input').val("");

    });

    if(urlUserPage == "profile") {
        showProfile();
    }
    else if(urlUserPage == "blacklist") {
        showBlacklist();
    }

};

function displayAlert(text, style) {

    $("html, body").animate({scrollTop: 0}, "slow");

    if ($('div.alerts:visible').children().length != 0) {
        $('div.alerts:visible').fadeOut("fast", function () {
            console.log("fadeout");
            $(this).empty()
                .append('<p>' + text + '</p>')
                .fadeIn();
        });
    } else {
        $('div.alerts:visible').empty()
            .append('<p>' + text + '</p>')
            .fadeIn();
    }
    $('div.alerts:visible').removeClass('alert_error alert_warning alert_success').addClass('alert_' + style);
    $('div.alerts:visible').click(function (e) {
        e.preventDefault();
        $(this).hide();
    });
}

function drawLeftHeader () {
    $('span.edible').text("Edible | " + currentUser.email);
}

var showProfile = function(e) {
    document.title = "Edible - Profil";
    if(window.history.state != "profile") {
        window.history.pushState("profile", "Edible - Profil", 'profile');
    }

    $('#button_profile').css("background-color", "#333");
    $("#button_blacklist").css("background-color", "#AAA");
    $('#button_help').hide();

    $('#profile_alerts').hide();
    $('#div_device_table').empty();

    var now = new Date();

    var device;
    currentUser.getDevices()
        .then(function (devices) {
            $('#div_device_table').append('<table id="table_devices"></table>');
            for (device = 0; device < devices.length; device++) {
                if ((devices[device].until * 1000) > now.getTime()) {
                    if (devices[device].authToken != currentUser.authToken) {
                        $('#table_devices').append('<tr class="device"><th>' + devices[device].name + '</th><td><button id="' + devices[device].authToken + '" class="button_delete">X</button></td><tr>');
                        $('#' + devices[device].authToken).click(function (e) {
                            e.preventDefault();

                            if(!confirm("Möchten Sie dieses Gerät wirklich entfernen?")) {
                                return;
                            }

                            $(this).parent().parent().remove();
                            User.deleteDevice(this.id);
                            displayAlert('Das Gerät wurde entfernt!', 'success');
                        });
                    } else {
                        $('#table_devices').append('<tr class="device_current"><th>' + devices[device].name + '</th><td>Dieses Gerät</td><tr>');
                    }
                }
            }

            $('.content').children().hide();
            $('.profile').show();
        })
        .catch(function (err) {
            console.log(err);
            displayAlert('Fehler beim Laden der Geräte!', 'error');
        });

    $('#button_profile').attr('disabled', 'disabled');
    $('#button_blacklist').attr('disabled', null);
}

var showBlacklist = function() {
    document.title = "Edible - Blacklist";
    if(window.history.state != "blacklist") {
        window.history.pushState("blacklist", "Edible - Blacklist", 'blacklist');
    }

    $('#button_help').show();
    $('#button_help').show();

    $('#button_blacklist').css("background-color", "#333");
    $("#button_profile").css("background-color", "#AAA");

    $('.div_new_devices').hide();
    $('.content').children().hide();
    $('.blacklist').show();
    if($('input.search').val() == "") {
        drawBlacklist();
    }
    $('#button_blacklist').attr('disabled', 'disabled');
    $('#button_profile').attr('disabled', null);
    $('#button_impressum').attr('disabled', null);
    $('#button_privacy').attr('disabled', null)
}