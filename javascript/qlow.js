$(document).ready(function () {
  const iframe = document.getElementById("inlineFrameExample");
  let iWindow = iframe.contentWindow;
  let info = $("#info");

  $(window).on("load", function () {
    // Run code
    let message = JSON.stringify({ description: "info", info: info.val() });
    iWindow.postMessage(message);
  });

  var fillButton = $('[name="fill"]');
  // var finishButton = $('[name="finish"]');
  // var form = $("#responseform");

  fillButton.on("click", function (e) {
    e.preventDefault();
    iWindow.postMessage(JSON.stringify({ description: "fill" }));
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

    if (JSON.parse(data).action == "answer") {
      $("*[id*=1_answer]:visible").each(function () {
        console.log("The answer is: " + JSON.parse(data).answer);
        $(this).val(JSON.parse(data).answer);
      });
    }
  });
});
