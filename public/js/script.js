/**
 * Created by lelouch on 11/9/16.
 */

const EXT_MODE = {
    "php": "php",
    "js": "javascript",
    "txt": "text",
    "": "text"
};
var dir = {
    list: function (dir) {
        $("#progressIPageLoad").show(100);
        $("#fileContent").remove();
        $.post("/getFolders", {dir: dir}, function (folders) {
            $("#file-viewer").hide(100);
            $("#table-card").show(100);
            if(folders.status == "failure") {
                $("body").snackbar({
                    alive: 5000,
                    content: folders.message
                });

                setTimeout(function () {
                    window.history.back();
                }, 5001);
                return;
            }

            $("#tableContent").html("");
            folders.sort(function (a, b) {
                if (a.name < b.name) return -1;
                if (a.name > b.name) return 1;
                return 0;
            });

            var length = folders.length;

            if(length == 0) {
                $("#tableContent").append("<tr>" +
                    "<td colspan='4' class='text-center'>No Contents In This Folder</td>" +
                    "</tr>");
                $("#progressIPageLoad").hide(100);
            }

            for (var i = 0; i < length; i++) {
                var toAppend = "<tr id='" + folders[i].path + "'>" +
                    "<td>" +
                    "<div class='checkbox checkbox-adv'>" +
                    "<label for='" + i + "'>" +
                    "<input class='access-hide' id='" + i + "' data-id='" + folders[i].path + "' type='checkbox'>" +
                    "<span class='checkbox-circle'></span>" +
                    "<span class='checkbox-circle-check'></span>" +
                    "<span class='checkbox-circle-icon icon'>done</span>" +
                    "</label>" +
                    "</div>" +
                    "</td>" +
                    "<td>";

                toAppend += (folders[i].type == "dir") ?
                "<i class='material-icons' style='font-size: 13px; line-height: 1; color: #9a9a9a;'>folder</i>" +
                "&nbsp;&nbsp;<a href='#folder=" + folders[i].path + "'>" +
                "" + folders[i].name + "" +
                "</a>" :
                "<i style='font-size: 13px; line-height: 1; color: #9a9a9a;' class='material-icons'>insert_drive_file</i>" +
                "&nbsp;&nbsp;<a href='#file=" + folders[i].path + "'>" +
                "" + folders[i].name + "" +
                "</a>";

                toAppend += "</td>" +
                    "<td>" + folders[i].size + "</td>" +
                    "<td>" + folders[i].lastModified + "</td>" +
                    "</tr>";

                $("#progressIPageLoad").hide(100);
                $("#tableContent").append(toAppend);
            }
        });
    },
    changeDir: function (dir) {
        this.list(dir);
        $("#folderPath").text(dir);
    }
};

var navigation = {
    onhashchange: function () {
        var hash = document.location.hash.substr(1);
        if(hash.length == 0) {
            return;
        }
        $("#fileContent").remove();
        hash = hash.split("=");
        var key = hash[0], value = hash[1];
        if(key == "folder") {
            dir.changeDir(value);
        } else if(key == "file") {
            file.open(value);
        }
    },
    onload: function () {
        var hash = document.location.hash.substr(1);
        if(hash.length == 0) {
            $.get("/getDefaultDir", function (defaultDir) {
                defaultDir = defaultDir.defaultDir;
                window.location.hash = "#folder=" + defaultDir;
            });
        } else {
            hash = hash.split("=");
            var key = hash[0], value = hash[1];
            if(key == "folder") {
                dir.changeDir(value);
            } else if(key == "file") {
                file.open(value);
            }
        }
    }
};

var file = {
    open: function (fileName) {
        $("#progressIPageLoad").show(100);
        $.post("/getFileDetails", {fileName: fileName}, function (fileDetails) {
            $("#saveButton").show();
            $("#folderPath").text(fileName);
            $("#table-card").hide(100);
            $("#file-viewer").show(100);
            var $fileContent = $("<textarea autocomplete=\"off\" autocorrect=\"off\" autocapitalize=\"off\" spellcheck=\"false\" class='fileContent' id='fileContent'></textarea>");
            if(!fileDetails.writable) {
                $fileContent.attr("readonly", "readonly");
                $("#saveButton").hide();
            }
            $("#content-container").append($fileContent);
            $("#fileContent").val(fileDetails.content);

            $("#fileDetails").html('<div class="tile-wrap">' +
                '<div class="tile tile-collapse">' +
                '<div data-target="#ui_tile_example_1" data-toggle="tile">' +
                '<div class="tile-side pull-left" data-ignore="tile">' +
                '<div class="avatar avatar-sm">' +
                '<span class="icon">info</span>' +
                '</div>' +
                '</div>' +
                '<div class="tile-inner">' +
                '<div class="text-overflow">' +
                'File Details</div>' +
                '</div>' +
                '</div>' +
                '<div class="tile-active-show collapse" id="ui_tile_example_1">' +
                '<div class="tile-sub">' +
                '<ul>' +
                '<li>File Size: ' + fileDetails.size + ' MiB</li>' +
                '<li>Created At: ' + fileDetails.createdAt + '</li>' +
                '<li>Last Modified: ' +  fileDetails.lastModified + '</li>' +
                '</ul>' +
                '</div>' +
                '<div class="tile-footer">' +
                '<div class="tile-footer-btn pull-left">' +
                '<a class="btn btn-flat waves-attach" data-toggle="tile" href="#ui_tile_example_1">' +
                '<span class="icon">close</span>&nbsp;Close</a>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>');
            $("#saveButton").on("click", function () {
                $("#progressIPageLoad").show(100);
                $.post("/saveFile", {fileName: $("#folderPath").text(), content: $("#fileContent").val()}, function (status) {
                    $("#progressIPageLoad").hide(100);
                    if(status.status) {
                        $("body").snackbar({
                            alive: 3000,
                            content: "<a data-dismiss='snackbar'>Dismiss</a><div class='snackbar-text'>The file has been successfully updated!<i class=\"icon\">done</i></div>"
                        });
                    } else {
                        $("body").snackbar({
                            alive: 3000,
                            content: "<a data-dismiss='snackbar'>Dismiss</a><div class='snackbar-text'>There was some problem while saving the file.</div>"
                        });
                    }
                });
            });

            $("#progressIPageLoad").hide(100);
        })
    }
};

var Editor = {
    /**
     * Courtesy of SO
     * @param areaId
     * @param text
     */
    insertAtCaret: function (areaId, text) {
        var txtarea = document.getElementById(areaId);
        if (!txtarea) { return; }

        var scrollPos = txtarea.scrollTop;
        var strPos = 0;
        var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
            "ff" : (document.selection ? "ie" : false ) );
        if (br == "ie") {
            txtarea.focus();
            var range = document.selection.createRange();
            range.moveStart ('character', -txtarea.value.length);
            strPos = range.text.length;
        } else if (br == "ff") {
            strPos = txtarea.selectionStart;
        }

        var front = (txtarea.value).substring(0, strPos);
        var back = (txtarea.value).substring(strPos, txtarea.value.length);
        txtarea.value = front + text + back;
        strPos = strPos + text.length;
        if (br == "ie") {
            txtarea.focus();
            var ieRange = document.selection.createRange();
            ieRange.moveStart ('character', -txtarea.value.length);
            ieRange.moveStart ('character', strPos);
            ieRange.moveEnd ('character', 0);
            ieRange.select();
        } else if (br == "ff") {
            txtarea.selectionStart = strPos;
            txtarea.selectionEnd = strPos;
            txtarea.focus();
        }

        txtarea.scrollTop = scrollPos;
    },

    maintainIndentation: function () {
        var prevLineNo = $("#fileContent").val().substr(0, $("#fileContent").prop("selectionStart")).split("\n").length - 2;
        var prevLine = $("#fileContent").val().split("\n")[prevLineNo];
        var indentLevel = prevLine.match(/^\s*/)[0]
        this.insertAtCaret("fileContent", indentLevel);
    }
};

var keysDown = {
    handler: function(e) {
        var keyCode = e.keyCode || e.which;

        if(keyCode == 9) {
            if($("#fileContent").is(":focus")) {
                e.preventDefault();
                Editor.insertAtCaret("fileContent", "    ");
            }
        }
    }
};

var keysUp = {
    handler: function (e) {
        var keyCode = e.keyCode || e.which;

        if(keyCode == 13) {
            Editor.maintainIndentation();
        }
    }
};

var index_JS = function () {
    window.onload = navigation.onload;
    $(window).on("hashchange", navigation.onhashchange);
    window.onkeydown = keysDown.handler;
    window.onkeyup = keysUp.handler;
};