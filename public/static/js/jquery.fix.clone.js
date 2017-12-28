(function (original) {

    jQuery.fn.clone = function () {

        var result = original.apply(this, arguments),

            my_textareas = this.find('textarea').add(this.filter('textarea')),

            result_textareas = result.find('textarea').add(result.filter('textarea')),

            my_selects = this.find('select').add(this.filter('select')),

            result_selects = result.find('select').add(result.filter('select'));

        for (var i = 0, l = my_textareas.length; i < l; ++i) $(result_textareas[i]).val($(my_textareas[i]).val());

        for (var i = 0, l = my_selects.length;   i < l; ++i) {

            for (var j = 0, m = my_selects[i].options.length; j < m; ++j) {

                if (my_selects[i].options[j].selected === true) {

                    result_selects[i].options[j].selected = true;

                }

            }

        }

        return result;

    };

}) (jQuery.fn.clone);