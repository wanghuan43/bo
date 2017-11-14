(function ($) {
    $.fn.extend({
        "initSearchable": function (searchable, modelName) {
            var container = this;
            var operators = new Array();
            var fields = searchable;
            var i = 0;
            var lists = new Array();
            for (var field in searchable) {
                lists[field] = i;
                i++;
                operators[field] = searchable[field].operators;
            }

            var getFieldFirstOperator = function (field) {
                for (var op in operators[field]) {
                    return op;
                }
            };

            var getOperatorHtml = function (field, i) {
                var html = '<select class="operators" name="operators[' + modelName + '][' + i + ']">';
                for (var o in operators[field]) {
                    html += '<option value="' + o + '">' + operators[field][o] + '</option>';
                }
                html += '</select>';
                return html;
            };

            var getValueHtml = function (field, i, operator) {
                if (operator == undefined) {
                    operator = getFieldFirstOperator(field);
                }
                if (fields[field].type == "date") {
                    return '<input type="text" name="values[' + modelName + '][' + i + '][]" class="values date" id="' + modelName + '-' + field + '"/>';
                }
                if (fields[field].type == "select") {
                    var html = '<select name="values[' + modelName + '][' + i + '][]" class="values">';
                    var options = fields[field].options;
                    for (var key in options) {
                        html += '<option value="' + key + '">' + options[key] + '</option>';
                    }
                    html += '</select>';
                    return html;
                }
                if (operator == 'between') {
                    return '<input type="text" name="values[' + modelName + '][' + i + '][]" class="values" style="width:100px"/>'
                        + '<input type="text" name="values[' + modelName + '][' + i + '][]" class="values" style="width: 100px"/>';
                } else {
                    return '<input type="text" name="values[' + modelName + '][' + i + '][]" class="values"/>';
                }
            };

            var getFieldHtml = function (field, i) {
                html = '<select class="fields" name="fields[' + modelName + '][' + i + ']">';
                for (var f in fields) {
                    if (field == f) {
                        html += '<option value="' + f + '" selected>' + fields[f].name + '</option>';
                    } else {
                        html += '<option value="' + f + '">' + fields[f].name + '</option>';
                    }
                }
                html += '</select>';
                html += getOperatorHtml(field, i);
                html += getValueHtml(field, i);
                return html;
            }

            var html = "";
            var i = 0;
            for (field in fields) {
                html += '<div class="search-block">';
                html += getFieldHtml(field, i);
                html += '</div>';
                i++;
            }

            $(this).html(html);

            var getExcludeField = function () {

                var fs = new Array();

                $(container).find("select.fields").each(function () {
                    fs[$(this).val()] = true;
                });

                for (var field in lists) {
                    if (fs[field] === undefined)
                        return field
                }
                return false;
            };

            var changeFields = function (el) {
                var f = $(el).val();
                var p = $(el).parent();
                var i = lists[f];
                var oldField = getExcludeField();
                var oi = lists[oldField];

                p.find('.operators').remove();
                p.find('.values').remove();
                p.append(getOperatorHtml(f, oi));
                p.append(getValueHtml(f, oi));
                p.find(".operators").change(function () {
                    changeOperator(this);
                });
                bindValueClickFunc(p);

                lists[f] = oi;
                lists[oldField] = i;

                var op = $(container).find(".search-block:eq(" + i + ")");
                op.html(getFieldHtml(oldField, i));
                op.find("select.fields").change(function () {
                    changeFields(this);
                });
                op.find(".operators").change(function () {
                    changeOperator(this);
                });
                bindValueClickFunc(op);

            };

            var changeOperator = function (el) {
                var p = $(el).parent();
                var val = $(el).val();
                var field = p.find(".fields").val();
                p.find(".values").remove();
                p.append(getValueHtml(field, lists[field], val));
                bindValueClickFunc(p);
            };

            var bindValueClickFunc = function (p) {
                $(p).find(".values.date").each(function () {
                    var range = false;
                    if ($(this).parent().find(".operators").val() == "between") {
                        range = "~";
                    }
                    laydate.render({
                        elem: "#" + $(this).attr("id"),
                        range: range,
                        format: "yyyy-MM-dd"
                    });
                });
            };

            $(this).find("select.fields").change(function () {
                changeFields(this);
            });

            $(this).find("select.operators").change(function () {
                changeOperator(this);
            });

            bindValueClickFunc(this);

        }
    });
})(jQuery);