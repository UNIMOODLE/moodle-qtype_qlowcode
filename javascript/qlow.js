$(document).ready(function () {
  const iframe = document.getElementById("inlineFrameExample");
  let iWindow = iframe.contentWindow;

  $(window).on("load", function () {
    // Run code
    iWindow.postMessage("answer");
  });

  var fillButton = $('[name="fill"]');
  // var finishButton = $('[name="finish"]');
  // var form = $("#responseform");

  fillButton.on("click", function (e) {
    e.preventDefault();
    iWindow.postMessage("fill");
    $(this).blur();
  });

  // finishButton
  //   .on("click", function (e) {})
  //   .promise()
  //   .done(function () {
  //     $("#responseform .content").append("Hello <i>world</i>!");
  //   });
  // This event listener will run when the embedded page sends us a message
  window.addEventListener("message", (event) => {
    // extract the data from the message event
    const { data } = event;

    console.log("Los datos son: " + data);

    if (JSON.parse(data).action == "answer") {
      $("*[id*=1_answer]:visible").each(function () {
        // $(this).val(JSON.stringify(data));
        // console.log(JSON.parse(data).answer);

        console.log("The answer is: " + JSON.parse(data).answer);
        $(this).val(JSON.parse(data).answer);
      });
    }
  });
});
