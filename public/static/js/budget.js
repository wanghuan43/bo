function insertTable(type) {
    var input = $(rclickTD).find("input"), col = parseInt($(input).attr("col")),
        row = parseInt($(input).attr("row")),
        t_col = parseInt($("#t_col").val()), t_row = parseInt($("#t_row").val()), html = "";
    switch (type) {
        case "col":
            var html = '', t_col = t_col + 1;
            html = setColName(t_col, html);
            $(settingTable).find("tr:eq(0)").before(html);
            $(settingTable).find("tr:eq(1)").remove();
            $(settingTable).find("tr").each(function (index, element) {
                $(element).find("td").each(function (i, e) {
                    var c = isNaN(parseInt($(e).attr("col"))) ? 0 : parseInt($(e).attr("col"));
                    if ((i + 1) > col) {
                        $(e).attr("col", c + 1);
                        $(e).find("input").attr("col", c + 1);
                    }
                });
            })
            for (var i = 1; i <= t_row; i++) {
                var td = '<td col="' + col + '"><div class="checkDiv"><input type="checkbox" col="' + col + '" row="' + i + '" class="cbTool"></div><input type="text" class="stcol" col="' + col + '" row="' + i + '" value=""></td>\n';
                $(settingTable).find("input[row='" + i + "'][col='" + (col + 1) + "']").parents("td").before(td);
            }
            $("#t_col").val(t_col);
            break;
        case "row":
            html = '<tr row="' + row + '">\n';
            html += '<td style="width: 40px;" align="center">' + row + '&nbsp;<input type="checkbox" class="tableCheck rowCheck" data="' + row + '"></td>\n';
            for (var i = 1; i <= t_col; i++) {
                html += '<td col="' + i + '"><div class="checkDiv"><input type="checkbox" col="' + i + '" row="' + row + '" class="cbTool"></div><input type="text" class="stcol" col="' + i + '" row="' + row + '" value=""></td>\n';
            }
            html += '</tr>\n';
            $(settingTable).find("tr").each(function (i, e) {
                if ((i + 1) > row) {
                    var r = parseInt($(e).attr("row"));
                    $(e).find("td:eq(0)").html($(e).find("td:eq(0)").html().replace(r + '&nbsp;', (r + 1) + '&nbsp;'));
                    $(e).attr("row", r + 1);
                    $(e).find(".rowCheck").attr("data", r + 1);
                    $(e).find("input").attr("row", r + 1);
                }
            });
            $(rclickTD).parent("tr").before(html);
            $("#t_row").val(t_row + 1);
            break;
    }
    bindClick();
    funChange();
}

function combine() {
    var len = $(".cbTool:checked").length, rowCount = 0, colCount = 0;
    var td = $(".cbTool:checked").first().parents("td"), colIndex = 99999,
        rowIndex = $(".cbTool:checked").first().attr("row"), rowLast = $(".cbTool:checked").last().attr("row"),
        colLast = 0;
    if (len <= 1) {
        custom.alert("请至少选择两个相邻单元格，才可以合并。");
        return false;
    }
    $(".cbTool:checked").each(function (i, e) {
        colIndex = parseInt($(e).attr("col")) < colIndex ? parseInt($(e).attr("col")) : colIndex;
        colLast = parseInt($(e).attr("col")) > colIndex ? parseInt($(e).attr("col")) : colIndex;
    });
    $(".stcol:checked").each(function (i, e) {
        var colspan = isNaN(parseInt($(e).parents("td").attr("colsapn"))) ? 0 : parseInt($(e).parents("td").attr("colsapn"));
        var rowspan = isNaN(parseInt($(e).parents("td").attr("rowspan"))) ? 0 : parseInt($(e).parents("td").attr("rowspan"));
        if (colspan > 0 || rowspan > 0) {
            custom.alert("合并单元格中不可以选择已合并过的单元格。请先拆分！");
            return false;
        }
    })
    var row1 = $(".stcol[row='" + rowIndex + "']:checked").length;
    for (var i = (parseInt(rowIndex) + 1); i < parseInt(rowLast); i++) {
        var row2 = $(".stcol[row='" + (i + 1) + "']:checked").length;
        if (row2 > 0) {
            rowCount++;
        }
        if (row1 != row2) {
            custom.alert("合并单元格不可跨行或者跨列");
            return false;
        }
    }
    var col1 = $(".stcol[col='" + colIndex + "']:checked").length;
    for (var i = (parseInt(colIndex) + 1); i < parseInt(colLast); i++) {
        var col2 = $(".stcol[col='" + i + "']:checked").length;
        if (col2 > 0) {
            colCount++;
        }
        if (col1 != col2) {
            custom.alert("合并单元格不可跨行或者跨列");
            return false;
        }
    }
    rowCount = rowLast - rowIndex + 1;
    colCount = colLast - colIndex + 1;
    $(".cbTool:checked").each(function (i, e) {
        if (i > 0) {
            $(e).parents("td").hide();
            $(e).parents("td").find(".stcol").val("");
        }
    });
    $(td).attr("colspan", colCount);
    $(td).attr("rowspan", rowCount);
    $("#settingTable input[type='checkbox']").prop("checked", false);
    $("#settingTable").attr("border", "0");
    setTimeout(function () {
        resetTable(td)
    }, 1);
    funChange();
}

function resetTable(td) {
    $("#settingTable").attr("border", "1");
    var height = (parseInt($(td).height()) + 2);
    $(td).find(".stcol").css({height: height + "px", lineHeight: height + "px"});
    $(td).find("div").css("height", height + "px");
}

function spliTable() {
    var len = $(".cbTool:checked").length;
    if (len <= 0) {
        custom.alert("请至少选择一个合并过的单元格，才可以拆分。");
        return false;
    }
    $(".cbTool:checked").each(function (i, e) {
        var colspan = isNaN(parseInt($(e).parents("td").attr("colsapn"))) ? 0 : parseInt($(e).parents("td").attr("colsapn"));
        var rowspan = isNaN(parseInt($(e).parents("td").attr("rowspan"))) ? 0 : parseInt($(e).parents("td").attr("rowspan"));
        if (rowspan <= 0 && colspan <= 0) {
            custom.alert("只能拆分合并过的单元格");
            return false;
        }
    })
    $(".cbTool:checked").each(function (i, e) {
        var colspan = parseInt($(e).parents("td").attr("colspan")),
            rowspan = parseInt($(e).parents("td").attr("rowspan"));
        var rowStart = parseInt($(e).attr("row")), rowEnd = rowspan == 1 ? rowStart : (rowStart + (rowspan - 1)),
            colStart = parseInt($(e).attr("col")), colEnd = colspan == 1 ? colStart : (colStart + colspan);
        for (var i = rowStart; i <= rowEnd; i++) {
            for (var j = colStart; j <= colEnd; j++) {
                $(".stcol[row='" + i + "'][col='" + j + "']").parents("td").show();
            }
        }
        $(e).parents("td").attr({
            rowspan: 0,
            colspan: 0,
        });
        $(".stcol[row='" + rowStart + "'][col='" + colStart + "']").css({
            height: "30px",
            lineHeight: "30px",
        });
        $(".stcol[row='" + rowStart + "'][col='" + colStart + "']").prev().css({
            height: "30px",
        });
        $("#settingTable input[type='checkbox']").prop("checked", false);
    })
}

function setTable(row, col, baseTable, ro) {
    var html = '<table id="settingTable" border="1">\n', ro = ro == undefined ? 1 : ro;
    html = setColName(col, html);
    if (baseTable.length > 0) {
        for (var i = 0; i < baseTable.length; i++) {
            var ii = i + 1;
            html += '<tr row="' + ii + '">\n';
            html += '<td style="width: 40px;" align="center">' + ii + '&nbsp;<input type="checkbox" class="tableCheck rowCheck" data="' + ii + '"></td>\n';
            for (var j = 0; j < baseTable[i].length; j++) {
                var jj = j + 1;
                var crspan = "";
                if (parseInt(baseTable[i][j].c_colspan) > 0) {
                    crspan += 'colspan="' + baseTable[i][j].c_colspan + '" ';
                }
                if (parseInt(baseTable[i][j].c_rowspan) > 0) {
                    crspan += 'rowspan="' + baseTable[i][j].c_rowspan + '" ';
                }
                if (baseTable[i][j].display == "none") {
                    crspan += 'style="display:"' + baseTable[i][j].display + '" ';
                }
                if (baseTable[i][j].readonly != "1" && ro != 1) {
                    crspan += 'readonly="readonly" ';
                }
                html += '<td col="' + jj + '" ' + crspan + '>' +
                    '<div class="checkDiv"><input type="checkbox" col="' + jj + '" row="' + ii + '" class="cbTool"></div>' +
                    '<input type="text" class="stcol" col="' + jj + '" row="' + ii + '" value="' + baseTable[i][j].c_value + '">' +
                    '</td>\n';
            }
            html += '</tr>\n';
        }
    } else {
        for (var i = 1; i <= row; i++) {
            html += '<tr row="' + i + '">\n';
            html += '<td style="width: 40px;" align="center">' + i + '&nbsp;<input type="checkbox" class="tableCheck rowCheck" data="' + i + '"></td>\n';
            for (var j = 1; j <= col; j++) {
                html += '<td col="' + j + '"><div class="checkDiv"><input type="checkbox" col="' + j + '" row="' + i + '" class="cbTool"></div><input type="text" class="stcol" col="' + j + '" row="' + i + '" value=""></td>\n';
            }
            html += '</tr>\n';
        }
    }
    html += '</table>\n';
    settingTable = $(html);
    $("#table").html(settingTable);
    bindClick();
}

function bindClick() {
    $(settingTable).find("input").unbind("click");
    $(settingTable).find("input").unbind("change");
    $(settingTable).find("td").unbind("mousedown");
    $(settingTable).find(".stcol").unbind("mouseover");
    $(settingTable).find(".stcol").unbind("mouseout");
    $(settingTable).find(".tableCheck").click(function () {
        var row = $(this).hasClass("rowCheck") ? $(this).attr("data") : 0;
        var col = $(this).hasClass("colCheck") ? $(this).attr("data") : 0;
        setCheck(row, col, $(this).prop("checked"));
    });
    $(settingTable).find("#checkAll").click(function () {
        setCheck(0, 0, $(this).prop("checked"));
    });
    $(settingTable).find(".stcol").change(function () {
        valueChange($(this));
        funChange(this);
    });
    $(settingTable).find("td").mousedown(function (e) {
        if (e.button == 2) {
            rclickTD = $(this);
        }
    });
    $(settingTable).find(".stcol").mouseover(function (event) {
        var data = $(this).attr("data");
        if (data != "" && data != undefined) {
            $(".showTips").html(data);
            $(".showTips").css({
                left: (parseFloat(event.clientX) + 10) + "px",
                top: (parseFloat(event.clientY) - 30) + "px",
                display: "block",
            });
        }
    });
    $(settingTable).find(".stcol").mouseout(function (event) {
        $(".showTips").hide();
    });
}

function funChange(thisElement) {
    $(settingTable).find(".stcol").each(function (i, e) {
        if ($(e).attr("data") != undefined && !$(thisElement).is($(e))) {
            var data = $(e).attr("data");
            $(e).val(data);
            valueChange($(e));
        }
    });
}

function valueChange(input) {
    var reg = /^(\=)(sum|average)\((.+?)\)/i, val = $.trim($(input).val()),
        td = $(input).parents("td");
    if (val.indexOf("=") == 0) {
        var search = reg.exec(val), fun = "";
        if (search !== null) {
            fun = search[2].toLocaleLowerCase();
            val = search[3];
        }
        $(input).attr("data", $.trim($(input).val()));
        val = arrangeValue(val, fun, input);
        if (val === false) {
            custom.alert("格式有误。")
            return false;
        }
        calculateValue(val, fun, input);
    } else {
        $(input).removeAttr("data");
    }
}

function calculateValue(val, fun, input) {
    var nums = 0, count = val.length;
    switch (fun) {
        case "sum":
            for (var i = 0; i < count; i++) {
                var cc = val[i].length;
                for (var j = 0; j < cc; j++) {
                    nums += parseFloat(val[i][j]);
                }
            }
            break;
        case "average":
            for (var i = 0; i < count; i++) {
                var cc = val[i].length;
                for (var j = 0; j < cc; j++) {
                    nums += parseFloat(val[i][j]);
                }
            }
            nums = count == 0 ? 0 : nums / count;
            break;
        default:
            nums = eval(val);
            break;
    }
    $(input).val(nums);
}

function arrangeValue(val, fun, input) {
    var all = [];
    if (fun != "") {
        var comma = val.split(",");
        for (var i = 0; i < comma.length; i++) {
            var tt = funExcelFormat(comma[i], input);
            if (tt === false) {
                return false;
            }
            all.push(tt);
        }
    } else {
        var tmp = val.match(/[a-z0-9]+/ig), count = tmp.length;
        for (var i = 0; i < count; i++) {
            var t = tmp[i].split("");
            if (isNaN(t[0])) {
                var col = splitCharToNum(tmp[i].match(/^[a-zA-Z]+|[0-9]+/g)[0].toLocaleLowerCase()),
                    num = parseInt(tmp[i].match(/^[a-zA-Z]+|[0-9]+/g)[1]),
                    v = parseInt($("input[class='stcol'][col='" + col + "'][row='" + num + "']").val());
                v = isNaN(v) ? 0 : v;
                val = val.replace(eval("\/" + tmp[i] + "\\b\/ig"), v);
            }
        }
        all = val.replace(/\=/, "");
    }
    return all;
}

function funExcelFormat(tmp, input) {
    var hasColon = tmp.indexOf(":") >= 0 ? true : false, tt = [];
    if (hasColon) {
        tmp = tmp.split(":");
        if (!/^[a-zA-Z0-9]*$/g.test(tmp[0])) {
            return false;
        }
        if (!/^[a-zA-Z0-9]*$/g.test(tmp[1])) {
            return false;
        }
        var charB = splitCharToNum(tmp[0].match(/^[a-zA-Z]+|[0-9]+/g)[0].toLocaleLowerCase()),
            numB = parseInt(tmp[0].match(/^[a-zA-Z]+|[0-9]+/g)[1]),
            charE = splitCharToNum(tmp[1].match(/^[a-zA-Z]+|[0-9]+/g)[0].toLocaleLowerCase()),
            numE = parseInt(tmp[1].match(/^[a-zA-Z]+|[0-9]+/g)[1])
            , count = (charE - charB) + 1;
        for (var b = 1, dd = charB; b <= count; b++, dd++) {
            for (var n = numB; n <= numE; n++) {
                if ($(input).is($("input[class='stcol'][col='" + dd + "'][row='" + n + "']"))) {
                    return false;
                }
                var p = parseFloat($("input[class='stcol'][col='" + dd + "'][row='" + n + "']").val());
                tt.push((isNaN(p) ? 0 : p));
            }
        }
    } else {
        var charB = splitCharToNum(tmp.match(/^[a-zA-Z]+|[0-9]+/g)[0].toLocaleLowerCase()),
            numB = parseInt(tmp.match(/^[a-zA-Z]+|[0-9]+/g)[1]);
        if ($(input).is($("input[class='stcol'][col='" + charB + "'][row='" + numB + "']"))) {
            return false;
        }
        var p = parseFloat($("input[class='stcol'][col='" + charB + "'][row='" + numB + "']").val());
        tt.push((isNaN(p) ? 0 : p));
    }
    return tt;
}

function splitCharToNum(val) {
    var base = 97, tmp = val.split(""), count = tmp.length, rtn = 0;
    for (var i = 0; i < count; i++) {
        rtn += (parseFloat(tmp[i].charCodeAt()) - base) + 1;
    }
    return rtn;
}

function setCheck(row, col, checked) {
    if (row == 0 && col == 0) {
        $("#table").find("input[type='checkbox']").prop("checked", checked);
    } else if (row == 0) {
        $(".cbTool[col='" + col + "']").prop("checked", checked);
    } else if (col == 0) {
        $(".cbTool[row='" + row + "']").prop("checked", checked);
    }
}

function setColName(col, html) {
    var asc = 65;
    var round = Math.ceil(col / 26);
    var left = col % 26;
    html += '<tr>\n<td align="center">全选<input type="checkbox" id="checkAll"></td>\n';
    for (var i = 0; i < round; i++) {
        var count = col < 26 ? col : (i == (round - 1) ? (round == 1 ? 26 : left) : 26);
        var before = i == 0 ? "" : String.fromCharCode(asc + (i - 1));
        for (var j = 0; j < count; j++) {
            html += '<td align="center" style="min-width: 40px;" align="center">' + before + String.fromCharCode(asc + j) + '&nbsp;<input type="checkbox" class="tableCheck colCheck" data="' + ((i + 1) * (j + 1)) + '"></td>\n';
        }
    }
    html += '</tr>\n';
    return html;
}

function permissions() {
    $(".permissionDiv").show();
}

$(".permissionDiv .pcd").click(function(){
    $(".permissionDiv").hide();
});