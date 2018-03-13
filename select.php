<?php
/**
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 1.01 of the Secret Technical Group' APU Course Registration Simulation.
 *
 * @category  Select
 * @package      PackageName
 * @author       TRAN Tuan Dung <tuantr10@apu.ac.jp>
 * @copyright    2012 The Secret Technical Group
 * @license      Not available
 * @version      1.01
 * @url        http://apmate.net
 * @since     File available since Release 1.00
 */

session_start();
require("method.php");
$method = new method;
/** Declare variables
 *  
 */
$announce=''; 
$dayfull=array('Mon'=>'Monday','Tue'=>'Tuesday','Wed'=>'Wednesday','Thu'=>'Thursday','Fri'=>'Friday','Sat'=>'Saturday');
$day=array(2=>'Mon','Tue','Wed','Thu','Fri','Sat');
$quarter=array(1=>'SP1','SP2','SP');
$subject_day=$day[$_SESSION['ses_day']];
$subject_quarter=$quarter[$_SESSION['ses_quarter']];
$subject_period=$_SESSION['ses_period'];
$stable_subject_quarter=$quarter[$_SESSION['ses_quarter']];

$dictionary = array(
  'en'=> array(
      "quarter"=>"Quarter",
      "day"=>"Day",
      "period"=>"Period",
      "grade"=>"Grade",
      "apm_field"=>"APM Field",
      "aps_field"=>"APS Field",
      "lecture_code"=>"Lecture code",
      "course"=>"course",
      "course_instructor"=>"Course Instructor",
      "language"=>"Language",
      "credit"=>"Credit",
      "vacancy"=>"Vacancy",
      "syllabus"=>"Syllabus"
    )
  ,
  'ja'=> array(
      "quarter"=>"学期",
      "day"=>"日",
      "period"=>"Period",
      "grade"=>"グレード",
      "apm_field"=>"APM 分野",
      "aps_field"=>"APS 分野",
      "lecture_code"=>"講義コード",
      "course"=>"講義名",
      "course_instructor"=>"担当教員",
      "language"=>"言語",
      "credit"=>"単位",
      "vacancy"=>"空席",
      "syllabus"=>"シラバス"
    )
);
require("connect.php");

function destroy_session() {
  $_SESSION['ses_day']='';
  $_SESSION['ses_quarter']='';
  $_SESSION['ses_period']='';
}
/** (2) Insert subject
* Which insert subject into record and increase user's credits which equivalent to that subject's credits
*/
function insert_subject($f_option) {
/**
 * If that subject hasn't been chosen then insert that subject into records
 */
    $sql_check_existing="  SELECT user_id,subject_id,subject_code 
                FROM records
                WHERE user_id='".$_SESSION['ses_userid']."'
                AND subject_code='".$f_option."'";
    $query_check_existing=mysql_query($sql_check_existing);
    if(mysql_num_rows($query_check_existing)==0) {
/*
* Insert that subject into records
*/
      $sql_insert_subject="INSERT INTO records(user_id,subject_id,subject_code,subject_quarter,subject_period,subject_day,record_credit) 
                  SELECT user_id,subject_id,subject_code,subject_quarter,subject_period,subject_day,subject_credit 
                  FROM users,subjects 
                  WHERE user_id='".$_SESSION['ses_userid']."'
                  AND subject_code='".$f_option."'";
      mysql_query($sql_insert_subject); 
/*
* Get that subject credits
*/
      $sql_getting_subject_credit="  SELECT DISTINCT subject_credit 
                      FROM subjects
                      WHERE subject_code='".$f_option."'";
      $query_getting_subject_credit=mysql_query($sql_getting_subject_credit);
      while($row_getting_subject_credit=mysql_fetch_assoc($query_getting_subject_credit)) {
/*
* Increase the user's credit by adding that subject's credit number.
*/
        $sql_plus_user_credit="  UPDATE users
                    SET user_credit=user_credit+".$row_getting_subject_credit['subject_credit']."
                    WHERE user_id='".$_SESSION['ses_userid']."'";
        mysql_query($sql_plus_user_credit);
      }
    }
}
/**
* End (2)
*/

/** (3) Delete subject
* 
* Which delete that subject from records and decrease user's credits which equivalent to that subject's credits
* Increase that subject's vacancy
*/
function delete_subject($deleting_quarter,$deleting_day,$deleting_period) {
/**
* Decrease user's credit number which equivalent to that subject's credit
*/

  $sql_getting_subject_credit="  SELECT DISTINCT record_credit 
                  FROM records
                  WHERE subject_quarter IN('".$deleting_quarter."','SP')
                  AND subject_period='".$deleting_period."'
                  AND subject_day='".$deleting_day."'
                  AND record_deleted='0'
                  AND user_id='".$_SESSION['ses_userid']."'";
  $query_getting_subject_credit=mysql_query($sql_getting_subject_credit);
  while($row_getting_subject_credit=mysql_fetch_assoc($query_getting_subject_credit)) {
    $sql_minus_user_credit="  UPDATE users
                  SET user_credit=user_credit-".$row_getting_subject_credit['record_credit']."
                  WHERE user_id='".$_SESSION['ses_userid']."'";
    mysql_query($sql_minus_user_credit);
  }
/**
* Check if the subject has already applied by the user or not, if yes then increase subject vacancy, if no then do nothing
*/
  $sql_check_applied="  SELECT DISTINCT record_id,record_applied
              FROM records
              WHERE user_id='".$_SESSION['ses_userid']."'
              AND subject_quarter IN('".$deleting_quarter."','SP')
              AND subject_period='".$deleting_period."'
              AND subject_day='".$deleting_day."'";
  $query_check_applied=mysql_query($sql_check_applied);
  $check_applied;
  while($row_check_applied=mysql_fetch_assoc($query_check_applied))
  {
    $check_applied=$row_check_applied['record_applied'];
  }
  if ($check_applied==1)
  {
  $sql_set_deleted="  UPDATE records
            SET record_deleted='1'
            WHERE user_id='".$_SESSION['ses_userid']."'
            AND subject_code IN
            (  
              SELECT DISTINCT subject_code 
              FROM subjects
              WHERE subject_quarter IN('".$deleting_quarter."','SP')
              AND subject_period='".$deleting_period."'
              AND subject_day='".$deleting_day."'
            )";
  mysql_query($sql_set_deleted);
  }
  else
  {
/* Delete that subject from records
 */  

  $sql_delete_subject="  DELETE FROM records 
              WHERE user_id='".$_SESSION['ses_userid']."'
              AND subject_code IN
              (
                SELECT DISTINCT subject_code FROM subjects 
                WHERE (subject_quarter='".$deleting_quarter."' or subject_quarter='SP')
                AND subject_period='".$deleting_period."'
                AND subject_day='".$deleting_day."'
              )";
  mysql_query($sql_delete_subject);
  }
}
/**
* End (3)
*/

/** Handling logout button
* 
*/
if(isset($_POST['logout']))
{
  $temp_credit=0;
  
    $sql_reset_delete="  UPDATE records
              SET record_deleted='0' 
              WHERE user_id='".$_SESSION['ses_userid']."'
              AND record_deleted='1'";
              
    mysql_query($sql_reset_delete);
    $sql_delete_unapplied="  DELETE FROM records
                WHERE record_applied='0'
                AND user_id='".$_SESSION['ses_userid']."'";
    $query_delete_unapplied=mysql_query($sql_delete_unapplied);
    $sql_get_total_credit=" SELECT DISTINCT record_credit,subject_code
                FROM records
                WHERE user_id='".$_SESSION['ses_userid']."'
                AND record_applied='1'
                AND record_deleted='0'";
    $query_get_total_credit=mysql_query($sql_get_total_credit);
    while($row_get_total_credit=mysql_fetch_assoc($query_get_total_credit))
    {
      $temp_credit+=$row_get_total_credit['record_credit'];
    }
    $set_user_credit="  UPDATE users
              SET user_credit='".$temp_credit."'
              WHERE user_id='".$_SESSION['ses_userid']."'";
    mysql_query($set_user_credit);
  header("location:index.php");
}
/** End handing logout button
 */


/** Handling submit button
* 
* adding, deleting, changing as of user's choice and re-direct to 'timetableadvance.php'
* @ses_subject_code  chosen subject's code
*  type      string
*  value -1    subject's canceled
* @ses_day      chosen subject's day
*  type      string
* @ses_period    chosen subject's period
*  type      integer
* @ses_quarter    chosen subject's quarter
*  type      string
*   value  1    SP1
*  value  2    SP2
*  value  3    SP
*/
if (isset($_POST['submit'])) {
  $option='';
  $option=$_POST['numbers'];
  $array_check_stuck=array();
  $check_stuck=0;
  $subject_name='';
  /*Get subject real duration*/

  if($option!=-1) {
    $sql_get_subject_duration_name="SELECT subject_quarter,subject_name
                    FROM subjects
                    WHERE subject_code='".$option."'";
    $query_get_subject_duration_name=mysql_query($sql_get_subject_duration_name);
    while($row_get_subject_duration_name=mysql_fetch_assoc($query_get_subject_duration_name)) {
      $subject_quarter=$row_get_subject_duration_name['subject_quarter'];
      $subject_name=$row_get_subject_duration_name['subject_name'];
    }
/**
* Check if the user changed from another subject or not
*/
    $sql_check_changing="  SELECT DISTINCT record_id
                FROM records
                WHERE user_id='".$_SESSION['ses_userid']."'
                AND subject_period='".$subject_period."'
                AND subject_day='".$subject_day."'
                AND record_deleted='0'"; 
    if ($subject_quarter!='SP') {
      $sql_check_changing.="  AND subject_quarter IN ('".$subject_quarter."','SP')";
    }
    $query_check_changing=mysql_query($sql_check_changing);
/**
 * Adding new subject
 */
            if(mysql_num_rows($query_check_changing)==0) {
/**
 * Check if that subject is making any duplicate record (stuck with other subjects) or not. 
 * If not then insert record, else do nothing.
 */              $duplicate_records=0;
                $sql_get_period_day="  SELECT DISTINCT subject_period,subject_day,subject_id
                            FROM subjects
                            WHERE subject_code='".$option."'";
                $query_get_period_day=mysql_query($sql_get_period_day);
                while($row_get_period_day=mysql_fetch_assoc($query_get_period_day)) {
                  $sql_check_stuck="  SELECT DISTINCT record_id
                            FROM records
                            WHERE user_id='".$_SESSION['ses_userid']."' 
                            AND subject_day='".$row_get_period_day['subject_day']."'
                            AND subject_period='".$row_get_period_day['subject_period']."'
                            AND record_deleted='0'";
                  if ($subject_quarter!='SP') {
                    $sql_check_stuck.="AND subject_quarter IN('".$subject_quarter."','SP')";
                  }
                  $query_check_stuck=mysql_query($sql_check_stuck);
                  $duplicate_records+=mysql_num_rows($query_check_stuck);
                }
                /**debugging*/
              /**debugging*/
              if($duplicate_records > 0) {
                $announce="<font color='red'>class[".$option." ".$subject_name."] is duplicated timetable.<br />Please choose another subject :)</font><br/>";
              } else { /* else insert that subject */
                insert_subject($option);
                destroy_session();
                header("location:timetableadvance.php");
              }
            } else {
/**
* Changing subject
*/
                $sql_get_period_day="  SELECT DISTINCT subject_period,subject_day,subject_id
                            FROM subjects
                            WHERE subject_code='".$option."'";
                $query_get_period_day=mysql_query($sql_get_period_day);
                while($row_get_period_day=mysql_fetch_assoc($query_get_period_day))
                {
                  $sql_check_stuck="  SELECT DISTINCT record_id,subject_code
                            FROM records
                            WHERE user_id='".$_SESSION['ses_userid']."'
                            AND subject_day='".$row_get_period_day['subject_day']."'
                            AND subject_period='".$row_get_period_day['subject_period']."'
                            AND record_deleted='0'";
                  if($subject_quarter!="SP") {
                    $sql_check_stuck.="AND subject_quarter IN('".$subject_quarter."','SP')";
                  }
                  $query_check_stuck=mysql_query($sql_check_stuck);
                  while($row_check_stuck=mysql_fetch_assoc($query_check_stuck)) {
                    $array_check_stuck[]=$row_check_stuck['subject_code'];
                  }
                }
                $check_stuck=0;
                $array_check_stuck=array_unique($array_check_stuck);
                foreach($array_check_stuck as $key=>$value) {
                  foreach($array_check_stuck as $_key=>$_value) {
                    if($value!=$_value) {
                      ++$check_stuck;
                    }
                  }
                }
                
                if($check_stuck==0) {
                  /*make sure that the user doesnt tick the same subject*/
                    $sql_check_same_subject="  SELECT record_id
                                  FROM records
                                  WHERE subject_code='".$option."'
                                  AND user_id='".$_SESSION['ses_userid']."'";
                    $query_check_same_subject=mysql_query($sql_check_same_subject);
                    if (mysql_num_rows($query_check_same_subject)>0) {
                      /* same subject*/
                      destroy_session();
                      header("location:timetableadvance.php");
                    } else { /*not same subject*/
                          delete_subject($subject_quarter,$subject_day,$subject_period);
                          if($subject_quarter=="SP")
                          {
                            delete_subject("SP1",$subject_day,$subject_period);
                            delete_subject("SP2",$subject_day,$subject_period);
                          }
                          insert_subject($option);
                          destroy_session();
                          header("location:timetableadvance.php");
                    }
                } else { /*if stuck output error*/
                  $announce="<font color='red'>class[".$option." ".$subject_name."] is duplicated timetable.<br />Please choose another subject :)</font><br/>";
                }
            }
  } else {
/**
* Subject cancel
*/
    $sql_check_empty="  SELECT DISTINCT record_id
              FROM records
              WHERE user_id='".$_SESSION['ses_userid']."'
              AND subject_quarter IN('".$subject_quarter."','SP')
              AND subject_day='".$subject_day."'
              AND subject_period='".$subject_period."'
              AND record_deleted='0'";
    $query_check_empty=mysql_query($sql_check_empty);
    if (mysql_num_rows($query_check_empty)>0) {
      delete_subject($subject_quarter,$subject_day,$subject_period);
      destroy_session();
      header("location:timetableadvance.php");
    } else {
      destroy_session();
      header("location:timetableadvance.php");
    }
  }

}
/** End handling submit button
* 
*/


/** Handling back button
* 
*/
if (isset($_POST['back'])) {
  header("location:timetableadvance.php");
}
/** End handling back button
* 
*/
// CREATING RADIO GROUP FUNCTION
function createRadio($r_name,$r_options,$r_value,$r_checked,$r_vacancy,$r_lang) {
  $r_name = htmlentities($r_name);
  $html ="";
  $odd_even=0;
  foreach($r_options as $label=>$value) {
     /* Styling background color, even row has white background while odd row has black background*/
    $label = htmlentities($label);
    if(($r_vacancy[$label]==0) or $r_lang[$label]=="J") {/*Disable this option*/
      if ($odd_even==0) {
        $html .= "<tr><td><input type='radio' ";//put disabled to gray-ed the options
        $odd_even=1;
      } else {
        $html .= "<tr bgcolor='F5F5F5'><td><input type='radio' ";//put disabled to gray-ed the options
        $odd_even=0;
      }
    } else {
      if ($odd_even==0) {
        $html .= "<tr><td><input type='radio' ";
        $odd_even=1;
      } else {
        $html .= "<tr bgcolor='F5F5F5'><td><input type='radio' ";
        $odd_even=0;
      }
    }
    if($r_value[$label]==$r_checked) {
      $html .= " name='".$r_name."' value='".$r_value[$label]."' checked='checked' /></td>".$value."</tr>"."\n";
    } else {
      $html .= " name='".$r_name."' value='".$r_value[$label]."' /></td>".$value."</tr>"."\n";
    }
  };
  return $html;
}

echo "<div style='position: absolute; left: 15%; width: 995px; height: 55px'>";

/** Site banner
*
*/
$method->print_header();
/**
* End site banner
*/
echo "<br/>";

//QUERY CHOOSE SUBJECT THAT MATCH THAT QUARTER, DAY AND PERIOD
//select day/period lecturecode lecture duration course language credits instructor vacancy
echo "<font size='6' color='2c2082'> 2017 SPRING ".$dayfull[$subject_day]." ".$subject_period."</font><br />";
echo "<form action='select.php' method='post'>";
echo "<table align='center' width='100%' border='1' cellpadding='3' cellspacing='0' >";
echo "<tr bgcolor='F5F5F5'>";
echo "<th>Select</th>";
echo "<th>".$dictionary[$_COOKIE['language']]['quarter']."</th>";
echo "<th>".$dictionary[$_COOKIE['language']]['day']."</th>";
echo "<th>".$dictionary[$_COOKIE['language']]['period']."</th>";
echo "<th>".$dictionary[$_COOKIE['language']]['grade']."</th>";
echo "<th>".$dictionary[$_COOKIE['language']]['apm_field']."</th>";
echo "<th>".$dictionary[$_COOKIE['language']]['aps_field']."</th>";
echo "<th>".$dictionary[$_COOKIE['language']]['lecture_code']."</th>";
echo "<th>".$dictionary[$_COOKIE['language']]['course']."</th>";
echo "<th>".$dictionary[$_COOKIE['language']]['course_instructor']."</th>";
echo "<th>".$dictionary[$_COOKIE['language']]['language']."</th>";
echo "<th>".$dictionary[$_COOKIE['language']]['credit']."</th>";
echo "<th>".$dictionary[$_COOKIE['language']]['vacancy']."</th>";
echo "<th>".$dictionary[$_COOKIE['language']]['syllabus']."</th>";
echo "</tr>";
echo "<tr bgcolor='F5F5F5'>";
echo "<td>";

/*Cancel option*/
$chosen_code='';
$sql_generate_checked= "SELECT DISTINCT record_id,subject_code
            FROM records 
            WHERE subject_quarter IN('".$stable_subject_quarter."','SP')
            AND subject_day='".$subject_day."' 
            AND subject_period='".$subject_period."'
            AND user_id='".$_SESSION['ses_userid']."'
            AND record_deleted='0'";
$query_generate_checked=mysql_query($sql_generate_checked); 

if(mysql_num_rows($query_generate_checked)==0) {
  echo "<input type='radio' name='numbers' value='-1' checked='checked' /></td>
      <td colspan='13' align='center'><font color='15164F'><b> Choose this option to cancel class </b></font></td>"."\n";
} else {
  echo "<input type='radio' name='numbers' value='-1' /></td>  <td colspan='13' align='center'> Choose this option to cancel class </td>"."\n";
  $row_generate_checked=mysql_fetch_assoc($query_generate_checked);
  $chosen_code=$row_generate_checked['subject_code'];
}
/*End cancel option*/
echo "</tr>";
$testing=array(); //SUBJECT CODE
$array_subject_vacancy=array();
$array_subject_lang=array();
$array=array();
$text='';
//change subject name here
if($_COOKIE['language']=='en') {
  $subject_name_query = 'subject_name';
  $subject_instructor_query = 'subject_instructor';
} else {
  $subject_name_query = 'subject_name_jap';
  $subject_instructor_query = 'subject_instructor_jap';
}
$sql="SELECT subject_quarter,subject_day,subject_period,subject_grade,subject_area_apm,subject_area_aps,
    subject_code,$subject_name_query,$subject_instructor_query,subject_language,subject_credit,subject_vacancy 
    FROM subjects
    WHERE subject_quarter IN('".$stable_subject_quarter."','SP')
    AND subject_day='".$subject_day."' 
    AND subject_period='".$subject_period."'";

$query=mysql_query($sql);
while($row=mysql_fetch_assoc($query)) {
  foreach($row as $label=>$value) {
    $text.="<td>".$value."</td>";
  }
  $syllabus_code=$row['subject_code'];
  $text.="<td><a href='https://portal2.apu.ac.jp/campusp/slbssbdr.do?value(risyunen)=2017&value(semekikn)=1&value(kougicd)=".$syllabus_code."' target='_tab'>Syllabus</a></td>";
  $array[]=$text;
  $text='';
  $testing[]=$row['subject_code'];
  $array_subject_vacancy[]=$row['subject_vacancy'];
  $array_subject_lang[]=$row['subject_language'];
}
echo "<br>";
// CREATING LIST OF SUBJECTS WITH RADIO BUTTON
//GET THAT DAY'S SUBJECT CODE IF CHOSEN
echo createRadio('numbers',$array,$testing,$chosen_code,$array_subject_vacancy,$array_subject_lang);
echo "</table>";
echo "<br/>";
if ($announce!="") {
  echo "<div style='height:48%;width:100%;border:1px solid #ccc;font:16px/26px Georgia, Garamond, Serif;overflow:auto;'>";
  echo $announce;
  echo "</div>";
} else {
  echo "<div style='height:48%;width:100%;border:1px solid #ccc;font:16px/26px Georgia, Garamond, Serif;overflow:auto;'>";
  echo "<font color='2c2082'>Please select the course you wish to register<br/>Choose the subject, then click the 'Confirm' button :D</font>";
  echo "</div>";
}
echo "<br/>";
echo "<form action='select.php' method='POST'>";
echo "<input type='image' name='submit' value='submit' src='image/b_confirm_e.gif'>&nbsp";
echo "<input type='image' name='back' value='back' src='image/b_back_e.gif'>";
echo "</form>";
echo "</form>";
echo "<br />";
echo "<br />";
echo "</div>";
?>
<?php include_once("analyticstracking.php") ?>