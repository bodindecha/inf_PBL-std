<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
    require_once($dirPWroot."resource/php/core/config.php");
?>
<!--  -->
<!--block-obj class="tab-selector" tab-type="g">
    <div class="tabs">
        <div class="tab" data-page="___">
            <div class="face" data-name="___">
                <i class="material-icons">___</i>
                <span>Create</span>
            </div>
            <div class="pop-label">
                <span>___</span>
            </div>
        </div>
    </div>
</block-obj-->
<!--  -->
<section class="pages" page-type="ng">
    <div class="page" path="group/new">
        <div class="message gray wrapper">
            <form class="form">
                <p>สามารถกรอก/แก้ไขข้อมูลด้านล่างภายหลังได้</p>
                <table class="group-info"><tbody>
                    <tr>
                        <td>ชื่อโครงงานภาษาไทย</td>
                        <td><input name="nameth" type="text" maxlength="150" pattern="[ก-๛0-9A-Za-z ()[\]{}\-!@#$%.,/&*+_?|]{3,150}"></td>
                    </tr>
                    <tr>
                        <td>ชื่อโครงงานภาษาอังกฤษ</td>
                        <td><input name="nameen" type="text" maxlength="150" pattern="[A-Za-z0-9ก-๛ ()[\]{}\-!@#$%.,/&*+_?|]{3,150}"></td>
                    </tr>
                    <tr>
                        <td>ครูที่ปรึกษา 1</td>
                        <td><input name="adv1" type="hidden"><input type="text" readonly onFocus="pUI.select.advisor(1)"></td>
                    </tr>
                    <tr>
                        <td>ครูที่ปรึกษา 2</td>
                        <td><input name="adv2" type="hidden"><input type="text" readonly onFocus="pUI.select.advisor(2)"></td>
                    </tr>
                    <tr>
                        <td>ครูที่ปรึกษา 3</td>
                        <td><input name="adv3" type="hidden"><input type="text" readonly onFocus="pUI.select.advisor(3)"></td>
                    </tr>
                    <tr>
                        <td>สาขาโครงงาน</td>
                        <td><select name="type">
                            <?php foreach (str_split(" ABCDEFGHIJKLM") as $et) echo '<option value="'.$et.'">'.pblcode2text($et)[$_COOKIE['set_lang']].'</option>'; ?>
                        </select></td>
                    </tr>
                </tbody></table>
                <div class="group split" style="gap: 10px;">
                    <button onClick="return PBL.createGroup(false)" class="red hollow full-x" type="reset">Restart</button>
                    <button onClick="return PBL.createGroup(true)" class="blue full-x" type="submit" style="min-width: 60%">Create</button>
                </div>
            </form>
        </div>
    </div>
    <div class="page" path="group/join">
        <form class="form wrapper message rainbow-bg">
            <div class="group spread">
                <input name="gjc" type="text" maxlength="6" size="6" pattern="[A-Za-z0-9]{6}" placeholder="Enter group code here" required>
                <button onClick="return PBL.joinGroup()" class="cyan">Join</button>
            </div>
            <center style="font-size: 0.8em;"><a href="javascript:" onClick="pUI.show.ungrouped()" draggable="false">เพื่อนที่ยังไม่มีกลุ่ม</a></center>
        </form>
    </div>
</section>
<section class="pages" page-type="hg">
    <div class="page" path="schedule">
        <br>
        <p>เสร็จสิ้น <output name="progress">0</output>%</p>
    </div>
    <div class="page" path="group/information">
        <div class="--message gray">
            <form class="form" onChange="pUI.form.btnState()">
                <table class="group-info"><tbody>
                    <tr>
                        <td>ชื่อโครงงานภาษาไทย</td>
                        <td><input name="nameth" type="text" maxlength="150" pattern="[ก-๛0-9A-Za-z ()[\]{}\-!@#$%.,/&*+_?|]{3,150}"></td>
                    </tr>
                    <tr>
                        <td>ชื่อโครงงานภาษาอังกฤษ</td>
                        <td><input name="nameen" type="text" maxlength="150" pattern="[A-Za-z0-9ก-๛ ()[\]{}\-!@#$%.,/&*+_?|]{3,150}"></td>
                    </tr>
                    <tr>
                        <td>ครูที่ปรึกษา 1</td>
                        <td><input name="adv1" type="hidden"><input type="text" readonly onFocus="pUI.select.advisor(1)"></td>
                    </tr>
                    <tr>
                        <td>ครูที่ปรึกษา 2</td>
                        <td><input name="adv2" type="hidden"><input type="text" readonly onFocus="pUI.select.advisor(2)"></td>
                    </tr>
                    <tr>
                        <td>ครูที่ปรึกษา 3</td>
                        <td><input name="adv3" type="hidden"><input type="text" readonly onFocus="pUI.select.advisor(3)"></td>
                    </tr>
                    <tr>
                        <td>สาขาโครงงาน</td>
                        <td><select name="type">
                            <?php foreach (str_split(" ABCDEFGHIJKLM") as $et) echo '<option value="'.$et.'">'.pblcode2text($et)[$_COOKIE['set_lang']].'</option>'; ?>
                        </select></td>
                    </tr>
                </tbody></table>
                <div class="group spread">
                    <button disabled onClick="return PBL.save.info()" class="blue" type="submit" style="min-width: 40%;">บันทึก (แก้ไข)</button>
                </div>
            </form>
        </div>
        <div class="message cyan score" style="display: none;">
            <span>ผลการประเมิน: <output name="net"></output> คะแนน</span>
        </div>
    </div>
    <div class="page" path="group/members">
        <div class="code">
            <p>รหัสโครงงาน</p>
            <div class="expand">
                <output name="gjc" data-title="โค้ดเข้ากลุ่ม"></output>
            </div>
            <div class="action form"><div class="group spread"><div class="group">
                <button onClick="pUI.show.code()" class="gray icon" data-title="ขยายโค้ด"><i class="material-icons">fullscreen</i></button>
                <button onClick="pUI.copy('code')" class="blue icon" data-title="คัดลอกโค้ด"><i class="material-icons">content_copy</i></button>
                <button onClick="pUI.copy('link')" class="blue icon" data-title="คัดลอกลิงก์"><i class="material-icons">link</i></button>
                <button onClick="pUI.show.QRcode()" class="cyan icon" data-title="แสดงคิดอาร์โค้ด"><i class="material-icons">qr_code</i></button>
            </div></div></div>
        </div>
        <p class="title-btn">สมาชิกกลุ่ม <button></button></p>
        <table class="list form slider">
            <tbody></tbody>
        </table>
        <div class="settings message black form" onChange="pUI.form.validate()" style="display: none;">
            <strong>การตั้งค่า</strong>
            <div class="group split">
                <div class="group">
                    <label for="ref_statusOpen">ปิดไม่รับสมาชิกเพิ่ม</label>
                    <input type="checkbox" name="statusOpen" id="ref_statusOpen" class="switch v2 emphasize">
                    <label for="ref_statusOpen">เปิดรับสมาชิกใหม่</label>
                </div>
                <button onClick="PBL.save.settings('statusOpen')" class="blue hollow">Apply</button>
            </div>
            <div class="group split">
                <div class="group">
                    <input type="checkbox" name="publishWork" id="ref_publishing" class="switch v2 emphasize">
                    <label for="ref_publishing">เผยแพร่โครงงาน</label>
                </div>
                <button onClick="PBL.save.settings('publishWork')" class="blue hollow">Apply</button>
            </div>
        </div>
    </div>
    <div class="page" path="file/documents">
        <div class="files table form">
            <table><thead><tr>
                <th>ประเภท</th><th>ไฟล์</th><th></th>
            </tr></thead><tbody>
                <tr>
                    <td>ใบความรู้</td>
                    <td>ความหมายของโครงงาน</td>
                    <td><div class="act-group">
                        <a role="button" class="cyan hollow icon" href="https://drive.google.com/a/bodin.ac.th/open?id=1v0BKZssKPVkD_CcGxeKR3GhN146zoCfL"><i class="material-icons">visibility</i>เปิดไฟล์</a>
                    </div></td>
                </tr>
                <tr>
                    <td>แหล่งข้อมูล</td>
                    <td>ข้อมูลเบื้องต้นเกี่ยวกับหัวข้อในการจัดทำโครงงาน</td>
                    <td><div class="act-group">
                        <a role="button" class="gray hollow icon" href="https://pbl.bodin.ac.th/aids/general"><i class="material-icons">open_in_new</i>เปิดดู</a>
                    </div></td>
                </tr>
                <tr>
                    <td>ตัวอย่าง</td>
                    <td>โครงงานที่น่าสนใจ</td>
                    <td><div class="act-group">
                        <a role="button" class="gray hollow icon" href="https://pbl.bodin.ac.th/aids/otherexample"><i class="material-icons">open_in_new</i>เปิดดู</a>
                    </div></td>
                </tr>
                <tr class="isInIS">
                    <td>ใบงาน</td>
                    <td>IS1-1 ประเด็นที่ต้องการศึกษา</td>
                    <td><div class="act-group">
                        <a role="button" class="cyan hollow icon" href="https://drive.google.com/a/bodin.ac.th/open?id=1N_nM3H1HQl6U7rt8pBsHdkEH9n-3TYd9"><i class="material-icons">visibility</i>เปิดไฟล์</a>
                    </div></td>
                </tr>
                <tr class="isInIS">
                    <td>ใบงาน</td>
                    <td>IS1-2 การระบุปัญหา</td>
                    <td><div class="act-group">
                        <a role="button" class="cyan hollow icon" href="https://drive.google.com/a/bodin.ac.th/open?id=1BR8aKYf7Ykbcky9LpOOCClfHBB6rm_bg"><i class="material-icons">visibility</i>เปิดไฟล์</a>
                    </div></td>
                </tr>
                <tr class="isInIS">
                    <td>ใบงาน</td>
                    <td>IS1-3 การระบุสมมติฐาน</td>
                    <td><div class="act-group">
                        <a role="button" class="cyan hollow icon" href="https://drive.google.com/a/bodin.ac.th/open?id=1VIEBBjt_Bex9RLdmcbJ99segm0jkZ-u9"><i class="material-icons">visibility</i>เปิดไฟล์</a>
                    </div></td>
                </tr>
                <tr>
                    <td>แบบฟอร์ม</td>
                    <td>รายงานโครงงาน PBL</td>
                    <td><div class="act-group">
                        <a role="button" class="gray hollow icon" href="https://pbl.bodin.ac.th/aids/forms"><i class="material-icons">open_in_new</i>เปิดดู</a>
                    </div></td>
                </tr>
                <tr>
                    <td>แบบฟอร์ม</td>
                    <td>แผนผังความคิดบูรณาการ 8 กลุ่มสาระการเรียนรู้</td>
                    <td><div class="act-group">
                        <div class="group center">
                            <span><i class="material-icons">download</i> ดาวน์โหลด</span>
                            <!--a role="button" class="green hollow" disabled><i class="material-icons">download</i>ดาวน์โหลด</a-->
                            <a role="button" class="red icon" href="https://drive.google.com/a/bodin.ac.th/uc?id=1Lhxo-1JbmbnBVjXMTqY4t281jz9Sk9U2&export=download" data-title="PDF" download><i class="fa fa-file-pdf-o"></i></a>
                            <a role="button" class="blue icon" href="https://drive.google.com/a/bodin.ac.th/uc?id=1whSk7B0CmgzCqavsw4XiMMoWzP00oxSl&export=download" data-title="Word" download><i class="fa fa-file-word-o"></i></a>
                        </div>
                    </div></td>
                </tr>
            </tbody></table>
        </div>
        <br>
        <p><b>ข้อมูลเพิ่มเติม</b> ในการส่งงาน หากไฟล์มีขนาดใหญ่สามารถนำไปลดขนาด(โดยไม่เสียคุณภาพ)ได้ที่ลิงก์ตามประเภทไฟล์ก่อนอัปโหลดได้ [<a href="https://ilovepdf.com/compress_pdf">pdf</a>] [<a href="https://iloveimg.com/compress-image">ภาพ</a>]</p>
    </div>
    <div class="page" path="file/assignment">
        <div class="work form"><table cellspacing="0"><tbody>

        </tbody></table></div>
        <iframe name="dlframe" hidden></iframe>
    </div>
    <div class="page" path="comments">
        <div class="chat message yellow">
            <div class="start"><button disabled hidden></button></div>
        </div>
    </div>
</section>