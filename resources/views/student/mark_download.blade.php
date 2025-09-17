<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Mark Sheet</title>
  <script src="{{asset('js/app.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.min.js"></script>
</head>
<body>
  <script>
    const exam = {
      id: "{{ $exam->id }}",
      name: "{{ $exam->name }}",
      exam_date: "{{ $exam->exam_date }}"
    };
    const student = {
      name: "{{ auth()->user()->student_name }}",
      username: "{{ auth()->user()->user_name }}"
    };
    const answers = @json($answers);

    function getAnswerRows(answerGroup) {
      const rows = [["QNo", "Key", "Ans", "Res"]]; 
      $.each(answerGroup, function (index, item) {
        let mark = '';
        if (item.answer_key === "DEL") mark = "DEL";
        else mark = item.mark == 4 ? "C" : (item.mark == -1 ? "W" : "L");
        rows.push([
          item.q_no,
          item.answer_key ?? "0",
          item.answer ?? "",
          mark
        ]);
      });
      return rows;
    }

    function styledTable(body) {
      return {
        table: { body },
        fontSize: 10,
        layout: {
          paddingTop: () => 0,
          paddingBottom: () => 0,
        },
        margin: [0,2,0,2]
      };
    }

    const tables = answers.map(ansGroup => styledTable(getAnswerRows(ansGroup)));
    const docDefinition = {
      content: [
        { text: "GREEN PARK COACHING CENTRE, NAMAKKAL", style: "header" },
        { text: "CHECK THE ANSWERS THAT YOU MARKED", style: "subheader" },
        {
          table: {
            widths: ["50%","50%"],
            body: [
              [`Student Name: ${student.name}`, `Exam Date: ${exam.exam_date}`],
              [`Subject: ${exam.name}`, `User Name: ${student.username}`],
              [{ text:`Test ID: ${exam.id}`, colSpan: 2 }, {}]
            ]
          },
          layout: "noBorders",
          fontSize: 10,
          margin: [0,5,0,10]
        },
        {
         columns: tables.map(tbl => ({ width: `${100/answers.length}%`, stack: [tbl] }))
        }
      ],
      styles: {
        header: { fontSize: 14, bold: true, alignment: "center", margin:[0,0,0,5] },
        subheader: { fontSize: 12, bold: true, alignment: "center", margin:[0,0,0,10] }
      },
      defaultStyle: { font: "Roboto" }
    };

    pdfMake.createPdf(docDefinition).download(`${exam.name}-${exam.exam_date}.pdf`, () => {
      window.location.href = "{{ route('student.marksheet') }}";
    });
  </script>
</body>
</html>
