<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8">
      <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
      <title>Mark Sheet</title>
      <style type="text/css">
        body{
            font-family: Helvetica, Arial, sans-serif;
             line-height:25px;
             font-size:13px;

      }
      @media print {

         .break {page-break-after: always;}

         }

       table {
        border-collapse: collapse;
        width: 100%;
      }

      .table th, .table thead th, .table td{
        border: 1px solid #2e2e2e;
        padding: 3px 0px 3px 0px;
        font-size:12px;
      }

      .break:before, .break:after{  
    display: block!important;
}


tr {
    page-break-inside: avoid;
}
 
    </style>
   </head>
<body>

     <table style="margin: 5px 0px 5px 0px;">
     </table>

     <h1 style="text-align: center;margin: 15px 0px;">GREEN PARK COACHING CENTRE, NAMAKKAL</h1>
     <h3 style="text-align: center;margin: 15px 0px;">CHECK THE ANSWERS THAT YOU MARKED</h3>

     <table style="font-size:16px;">
            <tr>
              <td width="50%">Student Name: <?php echo auth()->user()->student_name; ?></td>
              <td width="50%">Exam Date: <?php echo $exam->exam_date; ?></td>
            </tr>
            <tr>
                <td width="50%">Subject: <?php echo $exam->name; ?></td>
                <td width="50%">User Name: <?php echo auth()->user()->user_name; ?></td>
              </tr>
              <tr>
                <td colspan="2">Test ID: <?php echo $exam->id; ?></td>
              </tr>
        </tbody>
     </table>
     <div style="display: flex;margin: 10px 0px;">
     @foreach($answers as $answer)
     <table class="table" style="margin: 5px;text-align: center;">
            <tr>
                <th>QNo</th>
                <th>Key</th>
                <th>Ans</th>
                <th>Res</th>
            </tr>
            @foreach($answer as $key=>$item)
            <tr>
              <?php
              $mark = '';
              if($item->answer_key == "DEL"){
                  $mark = 'DEL';
              }
              else {
                  $mark = $item->mark == 4 ? 'C' : ($item->mark == -1 ? 'W' : 'L');
              }
              ?>
                <td>{{ $item->q_no }}</td>
                <td>{{ $item->answer_key==null ? 0 : $item->answer_key }}</td>
                <td>{{ $item->answer }}</td>
                <td>{{ $mark }}</td>
            </tr>
            @endforeach
     </table>
     @endforeach
    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
 <script type="text/javascript">
  var opt = {
    margin:[0.5,0,0,0],
    filename:"{{ $exam->name }}-{{ $exam->exam_date }}.pdf",
    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' },
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 }, 
    pagebreak: {  mode: ['css', 'legacy'], },
  };
html2pdf().set(opt).from(document.body).save().then(function(){
  window.location.href="{{ route('student.marksheet') }}";
});
</script>
</body>
</html>
