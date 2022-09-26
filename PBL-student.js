const ajax = async function(url, params=null, method="POST", resultType="json") {
    let opts = {
        url: url, type: method, resultType: resultType,
    }; if (params != null) opts.data = params;
    var response = /* $.ajax(opts); */ new Promise(function(resolve) {
        /* let req = new XMLHttpRequest();
        req.open(method, url);
        req.onload = () => resolve(JSON.parse(req.response));
        req.send(); */
        opts.success = function(result) { resolve(JSON.parse(result)); };
        $.ajax(opts);
    }); // return await response;
    var dat = await response;
    if (dat.success) return (typeof dat.info !== "undefined" ? dat.info : true);
    else dat.reason.forEach(em => app.ui.notify(1, em));
    return false;
};
const PBL = (function(d) {
    const cv = {
        API_URL: "/s/PBL/v2/api/", USER: top.USER,
        HTML: {
            "tab-menu": (path, name, icon) => '<div class="tab" data-page="'+path+'" onClick="PBL.openPage(this)"><div class="face"><i class="material-icons">'+icon+'</i><span>'+name+'</span></div><div class="pop-label"><span>'+name+'</span></div></div>',
            "timeline": (task, pd) => {
                if (!pd.sem) return '</div><br><p>ภาคเรียนที่ 2</p><div class="progress slider">';
                task = task.toString(); var output = '<div class="sec sec-'+pd.deadline+'"><div class="disp" data-title="'+pd.title+'"><span class="line"></span><label>'+task+'</label></div><p>กำหนดส่งภายใน</p><output deadline="'+pd.deadline+'"></output><div class="asgmt-list">';
                Object.keys(pd.works).forEach(work => {
                    output += '<div class="work"><i class="material-icons" work="'+work+'"></i><label>'+pd.works[work]+'</label></div>'
                }); output += '</div></div>';
                return output;
            }, "work-act": type => '<button class="blue" onClick="PBL.upload(\''+type+'\')" data-title="Replace with new file"><i class="fa fa-upload"></i></button>'+
                '<button class="gray" onClick="PBL.file.preview(\''+type+'\')" data-title="View file"><i class="material-icons">visibility</i></button>'+
                '<button class="yellow" onClick="PBL.file.print(\''+type+'\')" data-title="Print file"><i class="material-icons">print</i></button>'+
                '<button class="green" onClick="PBL.file.download(\''+type+'\')" data-title="Download file"><i class="material-icons">download</i></button>'+
                '<button class="red" onClick="PBL.file.remove(\''+type+'\')" data-title="Remove file"><i class="material-icons">delete</i></button>'
        },
        tab_menu: {
            ng: [
                ["group/new", "Create", "library_add"],
                ["group/join", "Join", "exit_to_app"]
            ],
            hg: [
                ["schedule", "Schedule", "schedule"],
                ["group/information", "Information", "library_books"],
                ["group/members", "Members", "group"],
                ["file/documents", "Documents", "folder"],
                ["file/assignment", "Submit work", "assignment"],
                ["comments", "Comments", "comment"]
            ]
        }, timeline: [
            {
                isPBL: true,
                title: "การจัดกลุ่ม",
                deadline: "A",
                works: {
                    "n1": "ชื่อโครงงาน",
                    "n2": "ครูที่ปรึกษา",
                    "n3": "สาขาโครงงาน"
                }, sem: 1
            }, {
                isPBL: true,
                title: "ส่งผังความคิด",
                deadline: "B",
                works: {
                    "mindmap": "ผังความคิดบูรณาการ 8 กลุ่มสาระการเรียนรู้"
                }, sem: 1
            }, {
                isPBL: false,
                title: "ส่งใบงาน IS",
                deadline: "C",
                works: {
                    "IS1-1": "IS1-1 ประเด็นที่สนใจ",
                    "IS1-2": "IS1-2 ปัญหาเกี่ยวกับประเด็นที่สนใจ",
                    "IS1-3": "IS1-3 สมมติฐานที่เกี่ยวข้อง"
                }, sem: 1
            }, {
                isPBL: true,
                title: "ส่งบทที่ 1-3",
                deadline: "D",
                works: {
                    "report-1": "รายงานโครงงานบทที่ 1",
                    "report-2": "รายงานโครงงานบทที่ 2",
                    "report-3": "รายงานโครงงานบทที่ 3"
                }, sem: 1
            }, {
                sem: 0
            }, {
                isPBL: true,
                title: "ส่งบทที่ 4-5",
                deadline: "E",
                works: {
                    "report-4": "รายงานโครงงานบทที่ 4",
                    "report-5": "รายงานโครงงานบทที่ 5"
                }, sem: 2
            }, {
                isPBL: true,
                title: "ส่งเล่มเต็มและอื่นๆ",
                deadline: "F",
                works: {
                    "report-all": "รวมเล่มรายงานโครงงาน",
                    "abstract": "บทคัดย่อโครงงาน"
                }, sem: 2
            }, {
                isPBL: true,
                title: "ส่งโปสเตอร์",
                deadline: "G",
                works: {
                    "poster" :"โปสเตอร์"
                }, sem: 2
            },
        ], MSG: {
            "delete-group": "Are you sure you want to delete this group and its progress (all your work) ?\nThis action can't be undone.",
            "delete-mbr": "Are you sure you want to delete this member?\nThis action can't be undone.",
            "newLeader": "Are you sure you want to set your friend as the new group leader?\nThis action can't be undone.",
            "del-work": filename => "Are you sure you want to delete group \""+filename+"\" file?\nThis action can't be undone."
        }, workload: {
            "mindmap": "แผนผังความคิดบูรณาการ 8 กลุ่มสาระการเรียนรู้",
            "IS1-1": "ใบงาน IS1-1 (ประเด็นที่ต้องการศึกษา)",
            "IS1-2": "ใบงาน IS1-2 (การระบุปัญหา)",
            "IS1-3": "ใบงาน IS1-3 (การระบุสมมติฐาน)",
            "report-1": "เล่มรายงานโครงงานบทที่ 1",
            "report-2": "เล่มรายงานโครงงานบทที่ 2",
            "report-3": "เล่มรายงานโครงงานบทที่ 3",
            "report-4": "เล่มรายงานโครงงานบทที่ 4",
            "report-5": "เล่มรายงานโครงงานบทที่ 5",
            "report-all": "รวมเล่มรายงานโครงงาน (ฉบับเต็ม)",
            "abstract": "บทคัดย่อโครงงาน",
            "poster": "โปสเตอร์"
        }, mbr_settings: ["statusOpen", "publishWork"]
    };
    var sv = {
        started: false, HTML: {}, current: {
            page: "", workType: "",
        }, state: { button_freeze: false,
            loadInfoOver: true, loadSettingsOver: true
        }, notifyJsInited: false, history: {
            unsavedPage: []
        }
    };
    var initialize = function() {
        if (!sv.started) {
            sv.started = true;
            // Check logged-in
            if (!cv.USER.length) return sys.auth.orize(true, true);
            // Initial group state
            sv.HTML["header-bar"] = $("main > .container").html();
            getStatus();
            // Set up Notify-js
            onUnsavedloadOverSetup();
        }
    }, btnAction = {
        freeze: function() {
            $("main .pages .page.current button, main .tab-selector").attr("disabled", "");
            sv.state["button_freeze"] = true;
        }, unfreeze: function() {
            if (sv.state["button_freeze"]) {
                $("main .pages .page.current button, main .tab-selector").removeAttr("disabled");
                sv.state["button_freeze"] = false;
                switch (sv.current["page"]) {
                    case "group/information": {
                        sv.state["loadInfoOver"] = false; confirmLeave(sv.current["page"]);
                    break; }
                    /* case "group/members": {
                        sv.state["loadSettingsOver"] = false; confirmLeave(sv.current["page"]);
                    break; } */
                }
            }
        }
    }, helpCentre = function(type = null) {
        if (type == null) app.ui.lightbox.open("mid", {title: "ความช่วยเหลือ", allowclose: true, autoclose: 60000, html: d.querySelector("main > .manual[hidden]").innerHTML});
        else {
            switch (type) {
                case "document": app.ui.notify(1, [2, "Help: Manual document is currently unavailable."]); break;
                case "mediaVDO": app.ui.notify(1, [2, "Help: Manual video playlist is currently unavailable."]); break;
                default: {
                    const helpWin = window.open($('main > .manual[hidden] a[onClick$="PBL.help(\''+type+'\')"]').attr("data-href"));
                }
            } app.ui.lightbox.close();
        }
    }, onUnsavedloadOverSetup = function() {
        if (!sv.notifyJsInited) {
            sv.notifyJsInited = true;
            $.notify.addStyle("PBL-unsaved", {html: '<div>'+
                '<div class="clearfix">'+
                    '<div class="title" data-notify-html="title"></div>'+
                    '<div class="form inline">'+
                        '<button class="yellow" name="keep">Keep unsaved changes</button>'+
                        '<button class="red hollow" name="load">Load updates</button>'+
                    '</div>'+
                '</div>'+
            '</div>'});
            $(document).on('click', 'main .notifyjs-PBL-unsaved-base [name="keep"]', function() { $(this).trigger('notify-hide'); });
            $(document).on('click', 'main .page[path="group/information"] .notifyjs-PBL-unsaved-base [name="load"]', function() {
                load_groupInfo();
                $(this).trigger('notify-hide');
            });
            $(document).on('click', 'main .page[path="group/members"] .notifyjs-PBL-unsaved-base [name="load"]', function() {
                if (sv.isLeader) {
                    $('main .page[path^="group/"] .settings [onClick^="PBL.save.settings"]').attr("disabled", "");
                    cv.mbr_settings.forEach(es => {
                        d.querySelector('main .page[path^="group/"] .settings [name="'+es+'"]').checked = (sv.groupSettings[es] == "Y");
                    }); sv.state["loadSettingsOver"] = true; checkUnsavedPage("group/members");
                } $(this).trigger('notify-hide');
            });
        }
    }, getStatus = async function() {
        await ajax(cv.API_URL+"status", {type: "get", act: "personal"}).then(function(status) {
            let loadPart = checkHashPath(status.isGrouped, "render");
            sv.status = status;
            sv.code = status.code;
            initialRender(loadPart);
        });
    }, checkHashPath = function(isGrouped=null, cb_val=null) {
        var hash = location.hash.substring(2), sendback = [null];
        if (hash.length) {
            var path = hash.split("/");
            if (isGrouped == false) {
                if (hash == "group/new") sendback = [hash];
                else if (hash == "group/join") sendback = [hash, null];
                else if (hash.match(/^group\/join\/[A-Z0-9]{6}$/)) sendback = ["group/join", path[2]];
            } else if (isGrouped == true) {
                if (hash.match(/^(schedule|group\/(information|members)|file\/(documents|assignment)|comments)$/)) sendback = [hash];
            } // if (sendback[0] != null) sv.current["page"] = sendback[0];
        } // if (cb_val == null || cb_val == "render") return sendback[0]; else
        return sendback;
    }, initialRender = function(loadPart) {
        if (loadPart[1] == null && loadPart[2] >= 0) {
            sv.status = {isGrouped: false};
            sv.code = null;
        } // Tab bar
        var dType = (sv.status.isGrouped ? "h" : "n");
        $("main > .container")
            .html(sv.HTML["header-bar"])
            .append('<div class="wrapper tab-selector"><div class="tabs"></div></div>')
            .append('<section class="pages"></section>');
        var tab_menu_holder = $("main > .container .tab-selector .tabs").css("--tab-count", cv.tab_menu[dType+"g"].length);
        cv.tab_menu[dType+"g"].forEach(em => tab_menu_holder.append(cv.HTML["tab-menu"](...em)));
        // Continue to section
        if (loadPart[0] == null) loadPart[0] = (sv.status.isGrouped ? "schedule" : "group/join");
        // Add pages
        $("main > .container .pages").load("/s/PBL/v2/blocks.html .pages[page-type="+dType+"g]", function() {
            $("main > .container > .pages").html($("main > .container .pages > .pages").html());
            PBL.openPage(loadPart[0], loadPart);
            if (dType == "h") {
                renderBlock("schedule", "block");
                renderBlock("file/documents", "checkIS");
                renderBlock("file/assignment", "readyTable");
                chatApp.start("std", [sv.code]); sv.chatInit = false;
            }
        });
    }, load_page = function(me, args=[]) {
        if (typeof me === "string") me = d.querySelector('main .tab-selector .tab[data-page="'+me+'"]');
        var pageURL = $(me).attr("data-page");
        if (pageURL == sv.current["page"]) return false;
        if (args.length <= 1 || true) history.replaceState(null, null, location.pathname+location.search+"#/"+pageURL);
        sv.current["page"] = pageURL;
        $("main .tab-selector .tab.active").removeClass("active"); $(me).addClass("active");
        $("main .pages .page.current").removeClass("current"); $('main .pages .page[path="'+pageURL+'"]').addClass("current");
        if (args.length > 1) args.shift();
        renderBlock(pageURL, ...args);
    }
    var renderBlock = function(object, ...params) {
        switch (object) {
            case "group/new": {
                // if (params[0] == null && typeof params[1] !== "undefined") action_message(params[1]);
            } break;
            case "group/join": {
                if (params[0] == null || params[0] == "group/join") $('main .page[path="group/join"] [name="gjc"]').focus();
                else if (params[0].length) {
                    $('main .page[path="group/join"] [name="gjc"]').val(params[0]);
                    $('main .page[path="group/join"] button').focus();
                }
            } break;
            case "schedule": {
                if (params[0] == "block") {
                    var task = 1, element = "";
                    cv.timeline.forEach(ep => {
                        if (!ep.sem || ep.isPBL || (!ep.isPBL && sv.status.requireIS)) {
                            element += cv.HTML.timeline(task++, ep);
                            if (!ep.sem) task--;
                        }
                    }); $('main .page[path="schedule"]')
                        .prepend('<div class="progress slider">'+element+'</div>')
                        .prepend('<p>ภาคเรียนที่ 1</p>');
                } else fill_timeline_status();
            } break;
            case "group/information": {
                if (sv.state["loadInfoOver"]) load_groupInfo();
                else $('main .page[path="group/information"] .form').notify({
                    title: "You have unsaved changes."
                }, {
                    className: "warning",
                    elementPosition: "bottom right",
                    autoHideDelay: 60000,
                    clickToHide: false,
                    style: "PBL-unsaved"
                }); sv.state["button_freeze"] = true;
            } break;
            case "group/members": {
                $('main .page[path="group/members"] .code output[name="gjc"]').val(sv.code);
                load_member();
            } break;
            case "file/documents": {
                if (params[0] == "checkIS") {
                    if (!sv.status.requireIS) $('main .page[path="file/documents"] .files tr.isInIS').remove();
                    $('main .page[path="file/documents"] .files .act-group a[download]').attr("draggable", "false");
                    if (isSafari) $('main .page[path="file/documents"] .files .act-group a[download]').attr("target", "_blank");
                    $('main .page[path="file/documents"] a:not([download])').attr("target", "_blank").attr("draggable", "false").each((idx, elm) => {
                        let targetURL = elm.href;
                        let redirector = "/go?url="+encodeURIComponent(targetURL);
                        elm.href = redirector;
                    });
                }
            } break;
            case "file/assignment": {
                if (params[0] == "readyTable") {
                    var worklist = "";
                    Object.keys(cv.workload).forEach(ew => {
                        if (!sv.status.requireIS && ew.substr(0, 2)=="IS") return;
                        worklist += '<tr><td><span class="--txtoe">'+cv.workload[ew]+'</span></td><td><div class="group center" data-work="'+ew+'">';
                        worklist += '</div></td><td center><output name="'+ew+'"></output></td></tr>';
                    }); $('main .page[path="file/assignment"] .work tbody').html(worklist);
                } else load_work_status();
            } break;
            case "comments": {
                if (!sv.chatInit) {
                    sv.chatInit = true;
                    chatApp.init();
                }
            } break;
        }
    }, action_message = function(msg) {
        var message = []; switch (msg) {
            case 0: message = [1, "Unable to perform action for you are now not in a group."]; break;
        } if (message.length) app.ui.notify(1, message);
    }, group_create = function(send) {
        (async function(mode) {
            if (!mode) $('main .page[path="group/new"] input, main .page[path="group/new"] select').val("");
            else {
                var data = {
                    nameth: $('main .page[path="group/new"] [name="nameth"]').val().trim().replaceAll("เเ", "แ"),
                    nameen: $('main .page[path="group/new"] [name="nameen"]').val().trim().replaceAll("เเ", "แ"),
                    adv1: $('main .page[path="group/new"] [name="adv1"]').val(),
                    adv2: $('main .page[path="group/new"] [name="adv2"]').val(),
                    adv3: $('main .page[path="group/new"] [name="adv3"]').val(),
                    type: $('main .page[path="group/new"] [name="type"]').val()
                };
                if (data.nameth.length && !data.nameth.match(/^[ก-๛0-9A-Za-z ()[\]{}\-!@#$%.,/&*+_?|]{3,150}$/)) {
                    app.ui.notify(1, [2, "Invalid Thai project name."]);
                    $('main .page[path="group/new"] [name="nameth"]').focus();
                } else if (data.nameen.length && !data.nameen.match(/^[A-Za-z0-9ก-๛ ()[\]{}\-!@#$%.,/&*+_?|]{3,150}$/)) {
                    app.ui.notify(1, [2, "Invalid English project name."]);
                    $('main .page[path="group/new"] [name="nameen"]').focus();
                } else if (!" ABCDEFGHIJKLM".includes(data.type)) {
                    app.ui.notify(1, [2, "Invalid project type."]);
                    $('main .page[path="group/new"] [name="type"]').focus();
                } else {
                    btnAction.freeze();
                    await ajax(cv.API_URL+"group", {type: "create", act: "new-group", param: data})
                        .then(un2group).then(btnAction.unfreeze);
                }
            }
        }(send)); return false;
    }, group_joinRequest = function() {
        (async function() {
            var code = $('main .page[path="group/join"] [name="gjc"]').val().trim().toUpperCase();
            if (!code.length) {
                app.ui.notify(1, [2, "Group code empty."]);
                $('main .page[path="group/join"] [name="gjc"]').focus();
            } else if (!code.match(/^[A-Z0-9]{6}$/)) {
                app.ui.notify(1, [2, "Invalid group code."]);
                $('main .page[path="group/join"] [name="gjc"]').focus();
            } else {
                btnAction.freeze();
                await ajax(cv.API_URL+"group", {type: "join", act: "existing-group", param: code})
                    .then(un2group).then(btnAction.unfreeze);
            }
        }()); return false;
    }
    var un2group = function(dat) {
        if (typeof dat.message !== "undefined") dat.message.forEach(em => app.ui.notify(1, em));
        if (dat.isGrouped) {
            sv.status = dat;
            sv.code = dat.code;
            initialRender([null]);
        }
    }, fill_timeline_status = async function() {
        // Deadline
        await ajax(cv.API_URL+"status", {type: "work", act: "deadline"}).then(function(dat) {
            if (dat) Object.keys(dat).forEach(ed => {
                $('main .page[path="schedule"] output[deadline="'+ed+'"]').val(dat[ed][0]);
            }); else $('main .page[path="schedule"] output[deadline]').val("ไม่พบข้อมูล");
            sv.deadlines = dat;
        });
        // Bar status
        await load_work_status().then(function() {
            var now = Date.now(), present = false;
            if (sv.workStatus) cv.timeline.forEach(ep => {
                if (ep.sem && (ep.isPBL || (!ep.isPBL && sv.status.requireIS))) {
                    var done = true, color = "p", deadline = new Date(sv.deadlines[ep.deadline]).getTime();
                    Object.keys(ep.works).forEach(ew => { done = (done && sv.workStatus[ew]); });
                    if (now > deadline) color = (done ? "e" : "m");
                    else if (!present) { color = (done ? "e" : "s"); present = !present; }
                    else color = (done ? "i" : "p");
                    $('main .page[path="schedule"] .sec-'+ep.deadline+' .disp').attr("class", "disp "+color);
                }
            }); else $('main .page[path="schedule"] .sec-'+ep.deadline+' .disp').attr("class", "disp p");
        }).then(function() { // Work percentage
            if ((sv.deadlines || true) && sv.workStatus) {
                if (!sv.status.requireIS) Object.keys(cv.workload)
                    .forEach(ew => { if (ew.match(/^IS\d-\d$/)) delete sv.workStatus[ew]; });
                var works = Object.keys(sv.workStatus).length,
                    workDone = Object.values(sv.workStatus).filter(ew => ew).length * 100;
                workDone = (workDone % works) ? Math.round(workDone/works*100)/100 : workDone/works;
                // console.info("Project progress: "+(workDone).toString()+"%");
                $('main .page[path="schedule"] output[name="progress"]').val(workDone);
            } // else sys.auth.orize(true, true);
        });
    }, load_work_status = async function() {
        await ajax(cv.API_URL+"status", {type: "work", act: "file"}).then(function(dat) {
            if (dat) {
                // Schedule
                $('main .page[path="schedule"] .asgmt-list .work i').each((idx, elm) => {
                    var status = dat[elm.getAttribute("work")];
                    $(elm).attr("class", "material-icons "+(status ? "g" : "r")).text(status ? "done" : "clear");
                });
                // Assignments
                Object.keys(dat).forEach(ew => {
                    if (!ew.match(/^n\d+$/)) {
                        $('main .page[path="file/assignment"] .work output[name="'+ew+'"]')
                            .attr("class", dat[ew] ? "y" : "n")
                            .val(dat[ew] ? "ส่งแล้ว" : "ไม่มีไฟล์");
                        var action = (dat[ew] ? cv.HTML["work-act"](ew)
                            : '<button class="blue hollow" onClick="PBL.upload(\''+ew+'\')"><i class="material-icons">add_circle</i>Upload attatchment</button>');
                        $('main .page[path="file/assignment"] .work [data-work="'+ew+'"]')
                            .html(action)
                            .attr("class", "group center"+(dat[ew] ? " action" : ""));
                    }
                });
            } else sys.auth.orize(true, true);
            sv.workStatus = dat;
        });
    }, load_groupInfo = async function() {
        await ajax(cv.API_URL+"information", {type: "group", act: "title"}).then(async function(dat) {
            if (typeof dat.isGrouped !== "undefined" && !dat.isGrouped) initialRender([null, null, 0]); else {
                if (dat.score) {
                    $('main .page[path="group/information"] .score').fadeIn();
                    $('main .page[path="group/information"] [name="net"]')
                        .text(dat.score)
                        .attr("class", "color-"+(" rrrog".split("")[dat.score]));
                } else {
                    $('main .page[path="group/information"] .score').fadeOut();
                    $('main .page[path="group/information"] [name="net"]')
                        .text("").removeAttr("class");
                } delete dat.score;
                Object.keys(dat).forEach(ei => $('main .page[path="group/information"] [name="'+ei+'"]').val(dat[ei] || "") );
                sv.state["loadInfoOver"] = true; checkUnsavedPage(sv.current["page"]);
                var advs = [dat["adv1"], dat["adv2"], dat["adv3"]].filter(ea => ea != null);
                if (advs.length) await ajax(cv.API_URL+"information", {type: "person", act: "teacher", param: advs.join(",")}).then(function(dat2) {
                    Object.keys(dat2.list).forEach(et =>
                        $('main .page[path="group/information"] [name="adv'+(advs.indexOf(et) + 1).toString()+'"] + input').val(dat2.list[et])
        ); }); } });
    }, update_groupInfo = function() {
        (async function() {
            var data = {
                nameth: $('main .page[path="group/information"] [name="nameth"]').val().trim().replaceAll("เเ", "แ"),
                nameen: $('main .page[path="group/information"] [name="nameen"]').val().trim().replaceAll("เเ", "แ"),
                adv1: $('main .page[path="group/information"] [name="adv1"]').val(),
                adv2: $('main .page[path="group/information"] [name="adv2"]').val(),
                adv3: $('main .page[path="group/information"] [name="adv3"]').val(),
                type: $('main .page[path="group/information"] [name="type"]').val()
            };
            if (data.nameth.length && !data.nameth.match(/^[ก-๛0-9A-Za-z ()[\]{}\-!@#$%.,/&*+_?|]{3,150}$/)) {
                app.ui.notify(1, [2, "Invalid Thai project name."]);
                $('main .page[path="group/information"] [name="nameth"]').focus();
            } else if (data.nameen.length && !data.nameen.match(/^[A-Za-z0-9ก-๛ ()[\]{}\-!@#$%.,/&*+_?|]{3,150}$/)) {
                app.ui.notify(1, [2, "Invalid English project name."]);
                $('main .page[path="group/information"] [name="nameen"]').focus();
            } else if (!" ABCDEFGHIJKLM".includes(data.type)) {
                app.ui.notify(1, [2, "Invalid project type."]);
                $('main .page[path="group/information"] [name="type"]').focus();
            } else {
                btnAction.freeze();
                await ajax(cv.API_URL+"group", {type: "update", act: "information", param: data}).then(function(dat) {
                    if (typeof dat.isGrouped !== "undefined" && !dat.isGrouped) initialRender([null, null, 0]); else {
                        if (typeof dat.message !== "undefined") dat.message.forEach(em => app.ui.notify(1, em));
                    }
                }).then(btnAction.unfreeze).then(function() {
                    $("main .pages .page.current button").attr("disabled", "");
                    sv.state["loadInfoOver"] = true; checkUnsavedPage(sv.current["page"]);
                });
            }
        }()); return false;
    }, load_member = async function() {
        await ajax(cv.API_URL+"information", {type: "group", act: "member"}).then(async function(dat) {
            if (typeof dat.isGrouped !== "undefined" && !dat.isGrouped) initialRender([null, null, 0]); else {
                // Member names
                await ajax(cv.API_URL+"information", {type: "person", act: "student", param: dat.list.join(",")}).then(function(dat2) {
                    var index = 1, listBody = ""; sv.isLeader = (cv.USER == dat2.list[0].ID);
                    dat2.list.forEach(es => {
                        listBody += '<tr><td>'+index.toString()+'.</td><td>'+es.fullname+' (<a href="/'+es.ID+'" target="_blank" draggable="false">'+es.nickname+'</a>)</td><td>เลขที่ '+es.number+'</td><td>';
                        if (index++ == 1) listBody += '<a role="button" class="default" disabled>หัวหน้ากลุ่ม</a>';
                        else if (sv.isLeader) listBody += '<div class="group">'+
                            '<button onClick="PBL.kick('+es.ID+')" class="red hollow">ลบชื่อออก</button>'+
                            '<button onClick="PBL.setLeader('+es.ID+')" class="yellow hollow icon" data-title="Set as leader">👑</button>'+
                            '</div>';
                        listBody += '</td></tr>';
                    }); $('main .page[path="group/members"] .list tbody').html(listBody);
                    if (sv.isLeader) $('main .page[path="group/members"] > p button').attr("onClick", "PBL.terminate(true)").attr("class", "yellow").text("ลบกลุ่ม");
                    else $('main .page[path="group/members"] > p button').attr("onClick", "PBL.terminate(false)").attr("class", "red").text("ออกจากกลุ่ม");
                }); // Settings
                if (sv.isLeader) {
                    sv.groupSettings = dat.settings;
                    $('main .page[path^="group/"] .settings').fadeIn();
                    // Lookup each
                    if (sv.state["loadSettingsOver"]) {
                        $('main .page[path^="group/"] .settings [onClick^="PBL.save.settings"]').attr("disabled", "");
                        cv.mbr_settings.forEach(es => {
                            d.querySelector('main .page[path^="group/"] .settings [name="'+es+'"]').checked = (sv.groupSettings[es] == "Y");
                        }); sv.state["loadSettingsOver"] = true; checkUnsavedPage(sv.current["page"]);
                    } else $('main .page[path^="group/"] .settings').notify({
                        title: "You have unsaved changes."
                    }, {
                        className: "warning",
                        elementPosition: "bottom center",
                        autoHideDelay: 60000,
                        clickToHide: false,
                        style: "PBL-unsaved"
                    });
                } else $('main .page[path^="group/"] .settings').fadeOut();
            }
        });
    }, leave_group = async function(destroy, param=null) {
        if (destroy || (sv.isLeader && param == cv.USER)) {
            if (!sv.isLeader) app.ui.notify(1, [3, "You must be a leader to delete the group."]);
            else if (confirm(cv.MSG["delete-group"])) await ajax(cv.API_URL+"group", {type: "delete", act: "void"}).then(function(dat) {
                if (typeof dat.message !== "undefined") dat.message.forEach(em => app.ui.notify(1, em));
                if (dat) initialRender([null, null, 1]);
            });
        } else if (confirm(cv.MSG["delete-mbr"])) await ajax(cv.API_URL+"group", {type: "delete", act: "member", param: (param || cv.USER)}).then(function(dat) {
            if (dat) {
                if (param != null) {
                    if (typeof dat.message !== "undefined") dat.message.forEach(em => app.ui.notify(1, em));
                    load_member();
                } else {
                    app.ui.notify(1, [0, "You left the group."]);
                    initialRender([null, null, 2]);
                }
            }
        });
    }, kick_member = function(studentID) {
        leave_group(false, studentID);
    }, promote_member = async function(studentID) {
        if (studentID == cv.USER) app.ui.notify(1, (sv.isLeader ? [1, "You are already a group leader."] : [2, "You can't promote yourself as a group leader."]));
        else if (confirm(cv.MSG["newLeader"])) await ajax(cv.API_URL+"group", {type: "update", act: "leader", param: studentID}).then(function(dat) {
            if (dat) {
                if (typeof dat.message !== "undefined") dat.message.forEach(em => app.ui.notify(1, em));
                load_member();
            }
        });
    }, update_groupSetting = async function(settingName) {
        if (typeof sv.groupSettings[settingName] === "undefined") app.ui.notify(1, [3, "Setting not found."]);
        else {
            var newValue = (function(getName) {
                if (cv.mbr_settings.includes(getName)) return (d.querySelector('main .page[path^="group/"] .settings [name="'+getName+'"]').checked ? "Y" : "N");
                return [null];
            }(settingName)),
                button = $('main .page[path^="group/"] .settings [onClick="PBL.save.settings(\''+settingName+'\')"]');
            if (newValue == [null]) app.ui.notify(1, [3, "Error validating your setting."]);
            if (sv.groupSettings[settingName] == newValue) {
                app.ui.notify(1, [1, "No change applies."]);
                button.attr("disabled", "");
            } else await ajax(cv.API_URL+"information", {type: "settings", act: "member", param: [settingName, newValue]}).then(function(dat) {
                if (typeof dat.message !== "undefined") dat.message.forEach(em => app.ui.notify(1, em));
                if (dat) button.attr("disabled", "");
            });
        }
    }, openUploadTab = function(workType) {
        if (workType == false) {
            PBL.save.file(false);
            return;
        } sv.current["workType"] = workType;
        app.ui.lightbox.open("mid", {allowclose: true, html: '<iframe src="upload" style="width:90vw;height:712px;border:none">Loading...</iframe>'});
    }, recieve_file = function(status) {
        app.ui.lightbox.close();
        if (status == "complete") load_work_status();
        sv.current["workType"] = "";
    }, preview_file = function(type) {
        app.ui.lightbox.open("mid", {title: "ไฟล์"+cv.workload[type], allowclose: true, html:
            '<iframe src="preview?file='+type+'" style="width:90vw;height:80vh;border:none">Loading...</iframe>'
        });
    }, download_file = async function(type) {
        var button = $('main .page[path="file/assignment"] .work [data-work="'+type+'"] button[onClick*="download"]');
        await ajax(cv.API_URL+"status", {type: "get", act: "fileLink", param: type}).then(function(dat) {
            if (typeof dat.isGrouped !== "undefined" && !dat.isGrouped) initialRender([null, null, 0]); else
            if (dat.download) {
                d.querySelector('main iframe[name="dlframe"]').src = dat.download;
                setTimeout(function() { button.removeAttr("disabled"); }, 5000);
            } else {
                button.removeAttr("disabled");
                app.ui.notify(1, [3, "There's a problem downloading your file."]);
            }
        }); button.attr("disabled", "");
    }, print_file = async function(type) {
        var button = $('main .page[path="file/assignment"] .work [data-work="'+type+'"] button[onClick*="print"]');
        await ajax(cv.API_URL+"status", {type: "get", act: "fileLink", param: type}).then(function(dat) {
            if (typeof dat.isGrouped !== "undefined" && !dat.isGrouped) initialRender([null, null, 0]); else {
				if (dat.print) {
                    dat.print = atob(dat.print);
                    (dat.print.match(/.+\.pdf$/) ? printJS(dat.print) : printJS(dat.print, "image"));
                } else app.ui.notify(1, [3, "There's a problem preparing your file for print."]);
                setTimeout(function() { button.removeAttr("disabled"); }, 500);
            }
        }); button.attr("disabled", "");
    }, remove_file = async function(type) {
        if (confirm(cv.MSG["del-work"](cv.workload[type]))) {
            var button = $('main .page[path="file/assignment"] .work [data-work="'+type+'"] button[onClick*="remove"]');
            await ajax(cv.API_URL+"main", {type: "work", act: "remove", param: type}).then(function(dat) {
                if (typeof dat.isGrouped !== "undefined" && !dat.isGrouped) initialRender([null, null, 0]); else
                if (!dat) button.removeAttr("disabled");
                load_work_status();
            }); button.attr("disabled", "");
        }
    }, confirmLeave = function(page) {
        if (!sv.history["unsavedPage"].includes(page)) sv.history["unsavedPage"].push(page);
        $(window).bind("beforeunload", function() {
            if (!sv.history["unsavedPage"].includes(sv.current["page"]))
                PBL.openPage(sv.history["unsavedPage"][sv.history["unsavedPage"].length - 1]);
            return null;
        });
    }, checkUnsavedPage = function(page) {
        /* var flush = true;
        cv.mbr_settings.forEach(sn => { flush = (flush && sv.state[sn]); });
        if (flush) app.io.confirm("unleave"); */
        let pagePos = sv.history["unsavedPage"].indexOf(page);
        if (pagePos > -1) sv.history["unsavedPage"].splice(pagePos, 1);
        if (!sv.history["unsavedPage"].length) app.io.confirm("unleave");
    };
    return {
        init: initialize,
        openPage: load_page,
        help: helpCentre,
        createGroup: group_create,
        joinGroup: group_joinRequest,
        terminate: leave_group,
        kick: kick_member,
        setLeader: promote_member,
        upload: openUploadTab,
        save: {
            info: update_groupInfo,
            settings: update_groupSetting,
            file: recieve_file
        }, file: {
            preview: preview_file,
            print: print_file,
            download: download_file,
            remove: remove_file
        }, // Export Internal
        btnAction,
        groupCode: () => sv.code,
        pageURL: () => sv.current["page"],
        uploadType: () => sv.current["workType"],
        setState: (name, value) => sv.state[name] = value,
        confirmLeave: confirmLeave
    };
}(document)); top.PBL = PBL;