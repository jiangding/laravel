/**
 * LCalendar绉诲姩绔棩鏈熸椂闂撮€夋嫨鎺т欢
 * 
 * version:1.7.1
 * 
 * author:榛勭
 * 
 * git:https://github.com/xfhxbb/LCalendar
 * 
 * Copyright 2016
 * 
 * Licensed under MIT
 * 
 * 鏈€杩戜慨鏀逛簬锛� 2016-6-12 17:22:20
 */
window.LCalendar = (function() {
    var MobileCalendar = function() {
        this.gearDate;
        this.minY = 1900;
        this.minM = 1;
        this.minD = 1;
        this.maxY = 2099;
        this.maxM = 12;
        this.maxD = 31;
    }
    MobileCalendar.prototype = {
        init: function(params) {
            this.type = params.type;
            this.trigger = document.querySelector(params.trigger);
            if (this.trigger.getAttribute("data-lcalendar") != null) {
                var arr = this.trigger.getAttribute("data-lcalendar").split(',');
                var minArr = arr[0].split('-');
                this.minY = ~~minArr[0];
                this.minM = ~~minArr[1];
                this.minD = ~~minArr[2];
                var maxArr = arr[1].split('-');
                this.maxY = ~~maxArr[0];
                this.maxM = ~~maxArr[1];
                this.maxD = ~~maxArr[2];
            }
            if (params.minDate) {
                var minArr = params.minDate.split('-');
                this.minY = ~~minArr[0];
                this.minM = ~~minArr[1];
                this.minD = ~~minArr[2];
            }
            if (params.maxDate) {
                var maxArr = params.maxDate.split('-');
                this.maxY = ~~maxArr[0];
                this.maxM = ~~maxArr[1];
                this.maxD = ~~maxArr[2];
            }
            this.bindEvent(this.type);
        },
        bindEvent: function(type) {
            var _self = this;
            //鍛煎嚭鏃ユ湡鎻掍欢
            function popupDate(e) {
                _self.gearDate = document.createElement("div");
                _self.gearDate.className = "gearDate";
                _self.gearDate.innerHTML = '<div class="date_ctrl slideInUp">' +
                    '<div class="date_btn_box">' +
                    '<div class="date_btn lcalendar_cancel">取消</div>' +
                    '<div class="date_btn lcalendar_finish">确定</div>' +
                    '</div>' +
                    '<div class="date_roll_mask">' +
                    '<div class="date_roll">' +
                    '<div>' +
                    '<div class="gear date_yy" data-datetype="date_yy"></div>' +
                    '<div class="date_grid">' +
                    '<div>年</div>' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<div class="gear date_mm" data-datetype="date_mm"></div>' +
                    '<div class="date_grid">' +
                    '<div>月</div>' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<div class="gear date_dd" data-datetype="date_dd"></div>' +
                    '<div class="date_grid">' +
                    '<div>日</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                document.body.appendChild(_self.gearDate);
                dateCtrlInit();
                var lcalendar_cancel = _self.gearDate.querySelector(".lcalendar_cancel");
                lcalendar_cancel.addEventListener('touchstart', closeMobileCalendar);
                var lcalendar_finish = _self.gearDate.querySelector(".lcalendar_finish");
                lcalendar_finish.addEventListener('touchstart', finishMobileDate);
                var date_yy = _self.gearDate.querySelector(".date_yy");
                var date_mm = _self.gearDate.querySelector(".date_mm");
                var date_dd = _self.gearDate.querySelector(".date_dd");
                date_yy.addEventListener('touchstart', gearTouchStart);
                date_mm.addEventListener('touchstart', gearTouchStart);
                date_dd.addEventListener('touchstart', gearTouchStart);
                date_yy.addEventListener('touchmove', gearTouchMove);
                date_mm.addEventListener('touchmove', gearTouchMove);
                date_dd.addEventListener('touchmove', gearTouchMove);
                date_yy.addEventListener('touchend', gearTouchEnd);
                date_mm.addEventListener('touchend', gearTouchEnd);
                date_dd.addEventListener('touchend', gearTouchEnd);
            }
            //鍒濆鍖栧勾鏈堟棩鎻掍欢榛樿鍊�
            function dateCtrlInit() {
                var date = new Date();
                var dateArr = {
                    yy: date.getFullYear(),
                    mm: date.getMonth(),
                    dd: date.getDate() - 1
                };
                if (/^\d{4}-\d{1,2}-\d{1,2}$/.test(_self.trigger.value)) {
                    rs = _self.trigger.value.match(/(^|-)\d{1,4}/g);
                    dateArr.yy = rs[0] - _self.minY;
                    dateArr.mm = rs[1].replace(/-/g, "") - 1;
                    dateArr.dd = rs[2].replace(/-/g, "") - 1;
                } else {
                    dateArr.yy = dateArr.yy - _self.minY;
                }
                _self.gearDate.querySelector(".date_yy").setAttribute("val", dateArr.yy);
                _self.gearDate.querySelector(".date_mm").setAttribute("val", dateArr.mm);
                _self.gearDate.querySelector(".date_dd").setAttribute("val", dateArr.dd);
                setDateGearTooth();
            }
            //鍛煎嚭骞存湀鎻掍欢
            function popupYM(e) {
                _self.gearDate = document.createElement("div");
                _self.gearDate.className = "gearDate";
                _self.gearDate.innerHTML = '<div class="date_ctrl slideInUp">' +
                    '<div class="date_btn_box">' +
                    '<div class="date_btn lcalendar_cancel">取消</div>' +
                    '<div class="date_btn lcalendar_finish">确定</div>' +
                    '</div>' +
                    '<div class="date_roll_mask">' +
                    '<div class="ym_roll">' +
                    '<div>' +
                    '<div class="gear date_yy" data-datetype="date_yy"></div>' +
                    '<div class="date_grid">' +
                    '<div>骞�</div>' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<div class="gear date_mm" data-datetype="date_mm"></div>' +
                    '<div class="date_grid">' +
                    '<div>鏈�</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                document.body.appendChild(_self.gearDate);
                ymCtrlInit();
                var lcalendar_cancel = _self.gearDate.querySelector(".lcalendar_cancel");
                lcalendar_cancel.addEventListener('touchstart', closeMobileCalendar);
                var lcalendar_finish = _self.gearDate.querySelector(".lcalendar_finish");
                lcalendar_finish.addEventListener('touchstart', finishMobileYM);
                var date_yy = _self.gearDate.querySelector(".date_yy");
                var date_mm = _self.gearDate.querySelector(".date_mm");
                date_yy.addEventListener('touchstart', gearTouchStart);
                date_mm.addEventListener('touchstart', gearTouchStart);
                date_yy.addEventListener('touchmove', gearTouchMove);
                date_mm.addEventListener('touchmove', gearTouchMove);
                date_yy.addEventListener('touchend', gearTouchEnd);
                date_mm.addEventListener('touchend', gearTouchEnd);
            }
            //鍒濆鍖栧勾鏈堟彃浠堕粯璁ゅ€�
            function ymCtrlInit() {
                var date = new Date();
                var dateArr = {
                    yy: date.getFullYear(),
                    mm: date.getMonth()
                };
                if (/^\d{4}-\d{1,2}$/.test(_self.trigger.value)) {
                    rs = _self.trigger.value.match(/(^|-)\d{1,4}/g);
                    dateArr.yy = rs[0] - _self.minY;
                    dateArr.mm = rs[1].replace(/-/g, "") - 1;
                } else {
                    dateArr.yy = dateArr.yy - _self.minY;
                }
                _self.gearDate.querySelector(".date_yy").setAttribute("val", dateArr.yy);
                _self.gearDate.querySelector(".date_mm").setAttribute("val", dateArr.mm);
                setDateGearTooth();
            }
            //鍛煎嚭鏃ユ湡+鏃堕棿鎻掍欢
            function popupDateTime(e) {
                _self.gearDate = document.createElement("div");
                _self.gearDate.className = "gearDatetime";
                _self.gearDate.innerHTML = '<div class="date_ctrl slideInUp">' +
                    '<div class="date_btn_box">' +
                    '<div class="date_btn lcalendar_cancel">取消</div>' +
                    '<div class="date_btn lcalendar_finish">确定</div>' +
                    '</div>' +
                    '<div class="date_roll_mask">' +
                    '<div class="datetime_roll">' +
                    '<div>' +
                    '<div class="gear date_yy" data-datetype="date_yy"></div>' +
                    '<div class="date_grid">' +
                    '<div>年</div>' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<div class="gear date_mm" data-datetype="date_mm"></div>' +
                    '<div class="date_grid">' +
                    '<div>月</div>' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<div class="gear date_dd" data-datetype="date_dd"></div>' +
                    '<div class="date_grid">' +
                    '<div>日</div>' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<div class="gear time_hh" data-datetype="time_hh"></div>' +
                    '<div class="date_grid">' +
                    '<div>时</div>' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<div class="gear time_mm" data-datetype="time_mm"></div>' +
                    '<div class="date_grid">' +
                    '<div>分</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' + //date_roll
                    '</div>' + //date_roll_mask
                    '</div>';
                document.body.appendChild(_self.gearDate);
                dateTimeCtrlInit();
                var lcalendar_cancel = _self.gearDate.querySelector(".lcalendar_cancel");
                lcalendar_cancel.addEventListener('touchstart', closeMobileCalendar);
                var lcalendar_finish = _self.gearDate.querySelector(".lcalendar_finish");
                lcalendar_finish.addEventListener('touchstart', finishMobileDateTime);
                var date_yy = _self.gearDate.querySelector(".date_yy");
                var date_mm = _self.gearDate.querySelector(".date_mm");
                var date_dd = _self.gearDate.querySelector(".date_dd");
                var time_hh = _self.gearDate.querySelector(".time_hh");
                var time_mm = _self.gearDate.querySelector(".time_mm");
                date_yy.addEventListener('touchstart', gearTouchStart);
                date_mm.addEventListener('touchstart', gearTouchStart);
                date_dd.addEventListener('touchstart', gearTouchStart);
                time_hh.addEventListener('touchstart', gearTouchStart);
                time_mm.addEventListener('touchstart', gearTouchStart);
                date_yy.addEventListener('touchmove', gearTouchMove);
                date_mm.addEventListener('touchmove', gearTouchMove);
                date_dd.addEventListener('touchmove', gearTouchMove);
                time_hh.addEventListener('touchmove', gearTouchMove);
                time_mm.addEventListener('touchmove', gearTouchMove);
                date_yy.addEventListener('touchend', gearTouchEnd);
                date_mm.addEventListener('touchend', gearTouchEnd);
                date_dd.addEventListener('touchend', gearTouchEnd);
                time_hh.addEventListener('touchend', gearTouchEnd);
                time_mm.addEventListener('touchend', gearTouchEnd);
            }
            //鍒濆鍖栧勾鏈堟棩鏃跺垎鎻掍欢榛樿鍊�
            function dateTimeCtrlInit() {
                var date = new Date();
                var dateArr = {
                    yy: date.getFullYear(),
                    mm: date.getMonth(),
                    dd: date.getDate() - 1,
                    hh: date.getHours(),
                    mi: date.getMinutes()
                };
                if (/^\d{4}-\d{1,2}-\d{1,2}\s\d{2}:\d{2}$/.test(_self.trigger.value)) {
                    rs = _self.trigger.value.match(/(^|-|\s|:)\d{1,4}/g);
                    dateArr.yy = rs[0] - _self.minY;
                    dateArr.mm = rs[1].replace(/-/g, "") - 1;
                    dateArr.dd = rs[2].replace(/-/g, "") - 1;
                    dateArr.hh = parseInt(rs[3].replace(/\s0?/g, ""));
                    dateArr.mi = parseInt(rs[4].replace(/:0?/g, ""));
                } else {
                    dateArr.yy = dateArr.yy - _self.minY;
                }
                _self.gearDate.querySelector(".date_yy").setAttribute("val", dateArr.yy);
                _self.gearDate.querySelector(".date_mm").setAttribute("val", dateArr.mm);
                _self.gearDate.querySelector(".date_dd").setAttribute("val", dateArr.dd);
                setDateGearTooth();
                _self.gearDate.querySelector(".time_hh").setAttribute("val", dateArr.hh);
                _self.gearDate.querySelector(".time_mm").setAttribute("val", dateArr.mi);
                setTimeGearTooth();
            }
            //鍛煎嚭鏃堕棿鎻掍欢
            function popupTime(e) {
                _self.gearDate = document.createElement("div");
                _self.gearDate.className = "gearDate";
                _self.gearDate.innerHTML = '<div class="date_ctrl slideInUp">' +
                    '<div class="date_btn_box">' +
                    '<div class="date_btn lcalendar_cancel">取消</div>' +
                    '<div class="date_btn lcalendar_finish">确定</div>' +
                    '</div>' +
                    '<div class="date_roll_mask">' +
                    '<div class="time_roll">' +
                    '<div>' +
                    '<div class="gear time_hh" data-datetype="time_hh"></div>' +
                    '<div class="date_grid">' +
                    '<div>鏃�</div>' +
                    '</div>' +
                    '</div>' +
                    '<div>' +
                    '<div class="gear time_mm" data-datetype="time_mm"></div>' +
                    '<div class="date_grid">' +
                    '<div>鍒�</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' + //time_roll
                    '</div>' +
                    '</div>';
                document.body.appendChild(_self.gearDate);
                timeCtrlInit();
                var lcalendar_cancel = _self.gearDate.querySelector(".lcalendar_cancel");
                lcalendar_cancel.addEventListener('touchstart', closeMobileCalendar);
                var lcalendar_finish = _self.gearDate.querySelector(".lcalendar_finish");
                lcalendar_finish.addEventListener('touchstart', finishMobileTime);
                var time_hh = _self.gearDate.querySelector(".time_hh");
                var time_mm = _self.gearDate.querySelector(".time_mm");
                time_hh.addEventListener('touchstart', gearTouchStart);
                time_mm.addEventListener('touchstart', gearTouchStart);
                time_hh.addEventListener('touchmove', gearTouchMove);
                time_mm.addEventListener('touchmove', gearTouchMove);
                time_hh.addEventListener('touchend', gearTouchEnd);
                time_mm.addEventListener('touchend', gearTouchEnd);
            }
            //鍒濆鍖栨椂鍒嗘彃浠堕粯璁ゅ€�
            function timeCtrlInit() {
                var d = new Date();
                var e = {
                    hh: d.getHours(),
                    mm: d.getMinutes()
                };
                if (/^\d{2}:\d{2}$/.test(_self.trigger.value)) {
                    rs = _self.trigger.value.match(/(^|:)\d{2}/g);
                    e.hh = parseInt(rs[0].replace(/^0?/g, ""));
                    e.mm = parseInt(rs[1].replace(/:0?/g, ""))
                }
                _self.gearDate.querySelector(".time_hh").setAttribute("val", e.hh);
                _self.gearDate.querySelector(".time_mm").setAttribute("val", e.mm);
                setTimeGearTooth();
            }
            //閲嶇疆鏃ユ湡鑺傜偣涓暟
            function setDateGearTooth() {
                var passY = _self.maxY - _self.minY + 1;
                var date_yy = _self.gearDate.querySelector(".date_yy");
                var itemStr = "";
                if (date_yy && date_yy.getAttribute("val")) {
                    //寰楀埌骞翠唤鐨勫€�
                    var yyVal = parseInt(date_yy.getAttribute("val"));
                    //p 褰撳墠鑺傜偣鍓嶅悗闇€瑕佸睍绀虹殑鑺傜偣涓暟
                    for (var p = 0; p <= passY - 1; p++) {
                        itemStr += "<div class='tooth'>" + (_self.minY + p) + "</div>";
                    }
                    date_yy.innerHTML = itemStr;
                    var top = Math.floor(parseFloat(date_yy.getAttribute('top')));
                    if (!isNaN(top)) {
                        top % 2 == 0 ? (top = top) : (top = top + 1);
                        top > 8 && (top = 8);
                        var minTop = 8 - (passY - 1) * 2;
                        top < minTop && (top = minTop);
                        date_yy.style["-webkit-transform"] = 'translate3d(0,' + top + 'em,0)';
                        date_yy.setAttribute('top', top + 'em');
                        yyVal = Math.abs(top - 8) / 2;
                        date_yy.setAttribute("val", yyVal);
                    } else {
                        date_yy.style["-webkit-transform"] = 'translate3d(0,' + (8 - yyVal * 2) + 'em,0)';
                        date_yy.setAttribute('top', 8 - yyVal * 2 + 'em');
                    }
                } else {
                    return;
                }
                var date_mm = _self.gearDate.querySelector(".date_mm");
                if (date_mm && date_mm.getAttribute("val")) {
                    itemStr = "";
                    //寰楀埌鏈堜唤鐨勫€�
                    var mmVal = parseInt(date_mm.getAttribute("val"));
                    var maxM = 11;
                    var minM = 0;
                    //褰撳勾浠藉埌杈炬渶澶у€�
                    if (yyVal == passY - 1) {
                        maxM = _self.maxM - 1;
                    }
                    //褰撳勾浠藉埌杈炬渶灏忓€�
                    if (yyVal == 0) {
                        minM = _self.minM - 1;
                    }
                    //p 褰撳墠鑺傜偣鍓嶅悗闇€瑕佸睍绀虹殑鑺傜偣涓暟
                    for (var p = 0; p < maxM - minM + 1; p++) {
                        itemStr += "<div class='tooth'>" + (minM + p + 1) + "</div>";
                    }
                    date_mm.innerHTML = itemStr;
                    if (mmVal > maxM) {
                        mmVal = maxM;
                        date_mm.setAttribute("val", mmVal);
                    } else if (mmVal < minM) {
                        mmVal = maxM;
                        date_mm.setAttribute("val", mmVal);
                    }
                    date_mm.style["-webkit-transform"] = 'translate3d(0,' + (8 - (mmVal - minM) * 2) + 'em,0)';
                    date_mm.setAttribute('top', 8 - (mmVal - minM) * 2 + 'em');
                } else {
                    return;
                }
                var date_dd = _self.gearDate.querySelector(".date_dd");
                if (date_dd && date_dd.getAttribute("val")) {
                    itemStr = "";
                    //寰楀埌鏃ユ湡鐨勫€�
                    var ddVal = parseInt(date_dd.getAttribute("val"));
                    //杩斿洖鏈堜唤鐨勫ぉ鏁�
                    var maxMonthDays = calcDays(yyVal, mmVal);
                    //p 褰撳墠鑺傜偣鍓嶅悗闇€瑕佸睍绀虹殑鑺傜偣涓暟
                    var maxD = maxMonthDays - 1;
                    var minD = 0;
                    //褰撳勾浠芥湀浠藉埌杈炬渶澶у€�
                    if (yyVal == passY - 1 && _self.maxM == mmVal + 1) {
                        maxD = _self.maxD - 1;
                    }
                    //褰撳勾銆佹湀鍒拌揪鏈€灏忓€�
                    if (yyVal == 0 && _self.minM == mmVal + 1) {
                        minD = _self.minD - 1;
                    }
                    for (var p = 0; p < maxD - minD + 1; p++) {
                        itemStr += "<div class='tooth'>" + (minD + p + 1) + "</div>";
                    }
                    date_dd.innerHTML = itemStr;
                    if (ddVal > maxD) {
                        ddVal = maxD;
                        date_dd.setAttribute("val", ddVal);
                    } else if (ddVal < minD) {
                        ddVal = minD;
                        date_dd.setAttribute("val", ddVal);
                    }
                    date_dd.style["-webkit-transform"] = 'translate3d(0,' + (8 - (ddVal - minD) * 2) + 'em,0)';
                    date_dd.setAttribute('top', 8 - (ddVal - minD) * 2 + 'em');
                } else {
                    return;
                }
            }
            //閲嶇疆鏃堕棿鑺傜偣涓暟
            function setTimeGearTooth() {
                var time_hh = _self.gearDate.querySelector(".time_hh");
                if (time_hh && time_hh.getAttribute("val")) {
                    var i = "";
                    var hhVal = parseInt(time_hh.getAttribute("val"));
                    for (var g = 0; g <= 23; g++) {
                        i += "<div class='tooth'>" + g + "</div>";
                    }
                    time_hh.innerHTML = i;
                    time_hh.style["-webkit-transform"] = 'translate3d(0,' + (8 - hhVal * 2) + 'em,0)';
                    time_hh.setAttribute('top', 8 - hhVal * 2 + 'em');
                } else {
                    return
                }
                var time_mm = _self.gearDate.querySelector(".time_mm");
                if (time_mm && time_mm.getAttribute("val")) {
                    var i = "";
                    var mmVal = parseInt(time_mm.getAttribute("val"));
                    for (var g = 0; g <= 59; g++) {
                        i += "<div class='tooth'>" + g + "</div>";
                    }
                    time_mm.innerHTML = i;
                    time_mm.style["-webkit-transform"] = 'translate3d(0,' + (8 - mmVal * 2) + 'em,0)';
                    time_mm.setAttribute('top', 8 - mmVal * 2 + 'em');
                } else {
                    return
                }
            }
            //姹傛湀浠芥渶澶уぉ鏁�
            function calcDays(year, month) {
                if (month == 1) {
                    year += _self.minY;
                    if ((year % 4 == 0 && year % 100 != 0) || (year % 400 == 0 && year % 4000 != 0)) {
                        return 29;
                    } else {
                        return 28;
                    }
                } else {
                    if (month == 3 || month == 5 || month == 8 || month == 10) {
                        return 30;
                    } else {
                        return 31;
                    }
                }
            }
            //瑙︽懜寮€濮�
            function gearTouchStart(e) {
                e.preventDefault();
                var target = e.target;
                while (true) {
                    if (!target.classList.contains("gear")) {
                        target = target.parentElement;
                    } else {
                        break
                    }
                }
                clearInterval(target["int_" + target.id]);
                target["old_" + target.id] = e.targetTouches[0].screenY;
                target["o_t_" + target.id] = (new Date()).getTime();
                var top = target.getAttribute('top');
                if (top) {
                    target["o_d_" + target.id] = parseFloat(top.replace(/em/g, ""));
                } else {
                    target["o_d_" + target.id] = 0;
                }
                target.style.webkitTransitionDuration = target.style.transitionDuration = '0ms';
            }
            //鎵嬫寚绉诲姩
            function gearTouchMove(e) {
                e.preventDefault();
                var target = e.target;
                while (true) {
                    if (!target.classList.contains("gear")) {
                        target = target.parentElement;
                    } else {
                        break
                    }
                }
                target["new_" + target.id] = e.targetTouches[0].screenY;
                target["n_t_" + target.id] = (new Date()).getTime();
                var f = (target["new_" + target.id] - target["old_" + target.id]) * 30 / window.innerHeight;
                target["pos_" + target.id] = target["o_d_" + target.id] + f;
                target.style["-webkit-transform"] = 'translate3d(0,' + target["pos_" + target.id] + 'em,0)';
                target.setAttribute('top', target["pos_" + target.id] + 'em');
                if (e.targetTouches[0].screenY < 1) {
                    gearTouchEnd(e);
                };
            }
            //绂诲紑灞忓箷
            function gearTouchEnd(e) {
                e.preventDefault();
                var target = e.target;
                while (true) {
                    if (!target.classList.contains("gear")) {
                        target = target.parentElement;
                    } else {
                        break;
                    }
                }
                var flag = (target["new_" + target.id] - target["old_" + target.id]) / (target["n_t_" + target.id] - target["o_t_" + target.id]);
                if (Math.abs(flag) <= 0.2) {
                    target["spd_" + target.id] = (flag < 0 ? -0.08 : 0.08);
                } else {
                    if (Math.abs(flag) <= 0.5) {
                        target["spd_" + target.id] = (flag < 0 ? -0.16 : 0.16);
                    } else {
                        target["spd_" + target.id] = flag / 2;
                    }
                }
                if (!target["pos_" + target.id]) {
                    target["pos_" + target.id] = 0;
                }
                rollGear(target);
            }
            //缂撳姩鏁堟灉
            function rollGear(target) {
                var d = 0;
                var stopGear = false;

                function setDuration() {
                    target.style.webkitTransitionDuration = target.style.transitionDuration = '200ms';
                    stopGear = true;
                }
                var passY = _self.maxY - _self.minY + 1;
                clearInterval(target["int_" + target.id]);
                target["int_" + target.id] = setInterval(function() {
                    var pos = target["pos_" + target.id];
                    var speed = target["spd_" + target.id] * Math.exp(-0.03 * d);
                    pos += speed;
                    if (Math.abs(speed) > 0.1) {} else {
                        var b = Math.round(pos / 2) * 2;
                        pos = b;
                        setDuration();
                    }
                    if (pos > 8) {
                        pos = 8;
                        setDuration();
                    }
                    switch (target.dataset.datetype) {
                        case "date_yy":
                            var minTop = 8 - (passY - 1) * 2;
                            if (pos < minTop) {
                                pos = minTop;
                                setDuration();
                            }
                            if (stopGear) {
                                var gearVal = Math.abs(pos - 8) / 2;
                                setGear(target, gearVal);
                                clearInterval(target["int_" + target.id]);
                            }
                            break;
                        case "date_mm":
                            var date_yy = _self.gearDate.querySelector(".date_yy");
                            //寰楀埌骞翠唤鐨勫€�
                            var yyVal = parseInt(date_yy.getAttribute("val"));
                            var maxM = 11;
                            var minM = 0;
                            //褰撳勾浠藉埌杈炬渶澶у€�
                            if (yyVal == passY - 1) {
                                maxM = _self.maxM - 1;
                            }
                            //褰撳勾浠藉埌杈炬渶灏忓€�
                            if (yyVal == 0) {
                                minM = _self.minM - 1;
                            }
                            var minTop = 8 - (maxM - minM) * 2;
                            if (pos < minTop) {
                                pos = minTop;
                                setDuration();
                            }
                            if (stopGear) {
                                var gearVal = Math.abs(pos - 8) / 2 + minM;
                                setGear(target, gearVal);
                                clearInterval(target["int_" + target.id]);
                            }
                            break;
                        case "date_dd":
                            var date_yy = _self.gearDate.querySelector(".date_yy");
                            var date_mm = _self.gearDate.querySelector(".date_mm");
                            //寰楀埌骞翠唤鐨勫€�
                            var yyVal = parseInt(date_yy.getAttribute("val"));
                            //寰楀埌鏈堜唤鐨勫€�
                            var mmVal = parseInt(date_mm.getAttribute("val"));
                            //杩斿洖鏈堜唤鐨勫ぉ鏁�
                            var maxMonthDays = calcDays(yyVal, mmVal);
                            var maxD = maxMonthDays - 1;
                            var minD = 0;
                            //褰撳勾浠芥湀浠藉埌杈炬渶澶у€�
                            if (yyVal == passY - 1 && _self.maxM == mmVal + 1) {
                                maxD = _self.maxD - 1;
                            }
                            //褰撳勾銆佹湀鍒拌揪鏈€灏忓€�
                            if (yyVal == 0 && _self.minM == mmVal + 1) {
                                minD = _self.minD - 1;
                            }
                            var minTop = 8 - (maxD - minD) * 2;
                            if (pos < minTop) {
                                pos = minTop;
                                setDuration();
                            }
                            if (stopGear) {
                                var gearVal = Math.abs(pos - 8) / 2 + minD;
                                setGear(target, gearVal);
                                clearInterval(target["int_" + target.id]);
                            }
                            break;
                        case "time_hh":
                            if (pos < -38) {
                                pos = -38;
                                setDuration();
                            }
                            if (stopGear) {
                                var gearVal = Math.abs(pos - 8) / 2;
                                setGear(target, gearVal);
                                clearInterval(target["int_" + target.id]);
                            }
                            break;
                        case "time_mm":
                            if (pos < -110) {
                                pos = -110;
                                setDuration();
                            }
                            if (stopGear) {
                                var gearVal = Math.abs(pos - 8) / 2;
                                setGear(target, gearVal);
                                clearInterval(target["int_" + target.id]);
                            }
                            break;
                        default:
                    }
                    target["pos_" + target.id] = pos;
                    target.style["-webkit-transform"] = 'translate3d(0,' + pos + 'em,0)';
                    target.setAttribute('top', pos + 'em');
                    d++;
                }, 30);
            }
            //鎺у埗鎻掍欢婊氬姩鍚庡仠鐣欑殑鍊�
            function setGear(target, val) {
                val = Math.round(val);
                target.setAttribute("val", val);
                if (/date/.test(target.dataset.datetype)) {
                    setDateGearTooth();
                } else {
                    setTimeGearTooth();
                }
            }
            //鍙栨秷
            function closeMobileCalendar(e) {
                e.preventDefault();
                var evt;
                try {
                    evt = new CustomEvent('input');
                } catch (e) {
                    //鍏煎鏃ф祻瑙堝櫒(娉ㄦ剰锛氳鏂规硶宸蹭粠鏈€鏂扮殑web鏍囧噯涓垹闄�)
                    evt = document.createEvent('Event');
                    evt.initEvent('input', true, true);
                }
                _self.trigger.dispatchEvent(evt);
                document.body.removeChild(_self.gearDate);
                _self.gearDate=null;
            }

            //鏃ユ湡纭
            function finishMobileDate(e) {
                var passY = _self.maxY - _self.minY + 1;
                var date_yy = parseInt(Math.round(_self.gearDate.querySelector(".date_yy").getAttribute("val")));
                var date_mm = parseInt(Math.round(_self.gearDate.querySelector(".date_mm").getAttribute("val"))) + 1;
                date_mm = date_mm > 9 ? date_mm : '0' + date_mm;
                var date_dd = parseInt(Math.round(_self.gearDate.querySelector(".date_dd").getAttribute("val"))) + 1;
                date_dd = date_dd > 9 ? date_dd : '0' + date_dd;
                _self.trigger.value = (date_yy % passY + _self.minY) + "-" + date_mm + "-" + date_dd;
                closeMobileCalendar(e);
            }
            //骞存湀纭
            function finishMobileYM(e) {
                var passY = _self.maxY - _self.minY + 1;
                var date_yy = parseInt(Math.round(_self.gearDate.querySelector(".date_yy").getAttribute("val")));
                var date_mm = parseInt(Math.round(_self.gearDate.querySelector(".date_mm").getAttribute("val"))) + 1;
                date_mm = date_mm > 9 ? date_mm : '0' + date_mm;
                _self.trigger.value = (date_yy % passY + _self.minY) + "-" + date_mm;
                closeMobileCalendar(e);
            }
            //鏃ユ湡鏃堕棿纭
            function finishMobileDateTime(e) {
                var passY = _self.maxY - _self.minY + 1;
                var date_yy = parseInt(Math.round(_self.gearDate.querySelector(".date_yy").getAttribute("val")));
                var date_mm = parseInt(Math.round(_self.gearDate.querySelector(".date_mm").getAttribute("val"))) + 1;
                date_mm = date_mm > 9 ? date_mm : '0' + date_mm;
                var date_dd = parseInt(Math.round(_self.gearDate.querySelector(".date_dd").getAttribute("val"))) + 1;
                date_dd = date_dd > 9 ? date_dd : '0' + date_dd;
                var time_hh = parseInt(Math.round(_self.gearDate.querySelector(".time_hh").getAttribute("val")));
                time_hh = time_hh > 9 ? time_hh : '0' + time_hh;
                var time_mm = parseInt(Math.round(_self.gearDate.querySelector(".time_mm").getAttribute("val")));
                time_mm = time_mm > 9 ? time_mm : '0' + time_mm;
                _self.trigger.value = (date_yy % passY + _self.minY) + "-" + date_mm + "-" + date_dd + " " + (time_hh.length < 2 ? "0" : "") + time_hh + (time_mm.length < 2 ? ":0" : ":") + time_mm;
                closeMobileCalendar(e);
            }
            //鏃堕棿纭
            function finishMobileTime(e) {
                var time_hh = parseInt(Math.round(_self.gearDate.querySelector(".time_hh").getAttribute("val")));
                time_hh = time_hh > 9 ? time_hh : '0' + time_hh;
                var time_mm = parseInt(Math.round(_self.gearDate.querySelector(".time_mm").getAttribute("val")));
                time_mm = time_mm > 9 ? time_mm : '0' + time_mm;
                _self.trigger.value = (time_hh.length < 2 ? "0" : "") + time_hh + (time_mm.length < 2 ? ":0" : ":") + time_mm;
                closeMobileCalendar(e);
            }
            _self.trigger.addEventListener('click', {
                "ym": popupYM,
                "date": popupDate,
                "datetime": popupDateTime,
                "time": popupTime
            }[type]);
        }
    }
    return MobileCalendar;
})()