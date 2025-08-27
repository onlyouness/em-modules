/**
 * 2007-2023 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

(function ($, JSON, w) {
    function removeData(dbType) {
        var keys = [], db;
        if (!w.sessionStorage || !w.localStorage) {
            return;
        }
        db = (dbType === 'local') ? w.localStorage : w.sessionStorage;
        for (var key in db) {
            if (key.indexOf('dumbFormState-') === 0) { keys.push(key); }
        }
        for (var i = 0; i < keys.length; i++) {
            delete db[keys[i]];
        }
    };
    $.fn.dumbFormState = function () {
        var $self = $(this), config, formKey,
        nonCheckableSelector = 'input[type="text"],input[type="password"],input[type="email"],input[type="hidden"],input[type="url"],input[type="tel"],input[type="search"],textarea,select',
        checkableSelector = 'input[type="checkbox"],input[type="radio"]',
        passwordSelector = 'input[type="password"]',
        remove = false;

        var input_types = ['color', 'date', 'datetime', 'datetime-local', 'email', 'month', 'number', 'range', 'search', 'tel', 'time', 'url', 'week'];
        for (var i in input_types) {
          var e = document.createElement('input');
          e.type = input_types[i];
          if (e.type == input_types[i]) {
            nonCheckableSelector = nonCheckableSelector + ',input[type="' + e.type + '"]';
          }
        }
        var allSelector = nonCheckableSelector + ',' + checkableSelector;

        function encode(str) {
            if(!str) { return ''; }
            return $('<div />').text(str).html().replace('"', '&quote;');
        }
        function persist($form, key) {
            var formData = [];
            $form.find(allSelector).each(function () {
                var $this = $(this);
                if (!config.skipSelector || !$this.is(config.skipSelector)) {
                    if ($this.is(nonCheckableSelector)) {
                        if ($this.is(passwordSelector) && !config.persistPasswords) { return; }
                        formData[formData.length] = {
                            selector: $this[0].nodeName.toLowerCase() +
                                    '[name="' + encode($this.attr('name')) + '"]',
                            val: $this.val()
                        };
                    } else if ($this.is(checkableSelector)) {
                        if ($this.attr('checked')) {
                            formData[formData.length] = {
                                selector: $this[0].nodeName.toLowerCase() +
                                        '[name="' + encode($this.attr('name')) + '"][value="' + encode($this.val()) + '"]',
                                val: 'checked'
                            };
                        }
                    }
                }
            });
            db[key] = JSON.stringify(formData);
        }
        if (typeof(w.sessionStorage) === 'undefined' || typeof(w.localStorage) === 'undefined') {
            return $self;
        }
        if ($self.data('dumbFormState-defined')) {
            config = $self.data('dumbFormState-config');
            if (arguments.length > 0 && $.isPlainObject(arguments[0])) {
                config = $.extend(config, arguments[0]);
                return $self;
            } else if (arguments.length > 0 && arguments[0] === 'remove') {
                remove = true;
            } else {
                return $self;
            }

        } else {
            $self.data('dumbFormState-defined', true);
            config = {
                persistPasswords: false,
                skipSelector: null,
                persistLocal: false,
                autoPersist: true,
				persistDomain: false
            };
            if (arguments.length > 0 && $.isPlainObject(arguments[0])) {
                config = $.extend(config, arguments[0]);
            }
            $self.data('dumbFormState-config', config);
        }
        db = config.persistLocal ? w.localStorage : w.sessionStorage;
        dm = config.persistDomain ? document.domain : window.location.pathname;
        $('form').each(function () {
            var $this = $(this);
            $this.data('dumbFormState-index', $this.index());
        });
        $self.each(function () {
            var $this = $(this),
            key = 'dumbFormState-' + dm + '-' + $this.data('dumbFormState-index'),
            dbObj = db[key], persistTimeout = null;
            if ($this[0].nodeName !== 'FORM') {
                throw 'dumbFormState - must be called on form elements only';
            }
            if (remove) {
                $this.unbind('blur.dumbFormState focus.dumbFormState click.dumbFormState keyup.dumbFormState submit.dumbFormState change.dumbFormState');
                delete db[key];
                return;
            }
            if (dbObj) {
                dbObj = $.parseJSON(dbObj);
                for (var i = 0; i < dbObj.length; i++) {
                    $this.find(dbObj[i].selector).each(function () {
                        var $this = $(this);
                        if ($this.is(checkableSelector)) {
                            $this.attr('checked', true);
                        } else {
                            try { $this.val(dbObj[i].val); } catch (e) { }
                        }
                    });
                }
            }
            $this.bind('submit.dumbFormState', function (ev) {
                persist($this, key);
            });
            if (config.autoPersist) {
                $this.bind('blur.dumbFormState focus.dumbFormState click.dumbFormState keyup.dumbFormState change.dumbFormState', function () {
                    if (persistTimeout !== null) {
                        window.clearTimeout(persistTimeout);
                        persistTimeout = null;
                    }
                    persistTimeout = window.setTimeout(function () { persist($this, key); }, 250);
                });
            }
        });
        return $self;
    };
    $.fn.dumbFormState.removeSession = function () {
        removeData('session');
    };
    $.fn.dumbFormState.removeLocal = function () {
        removeData('local');
    };
    $.fn.dumbFormState.removeAll = function () {
        $.fn.dumbFormState.removeSession();
        $.fn.dumbFormState.removeLocal();
    };
})(jQuery, JSON, window);
