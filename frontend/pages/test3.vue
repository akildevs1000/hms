<template>
  <div>
    <div id="myDiv">
      <p>Centered text</p>
    </div>

    <div id="myMenu" style="display: none">
      <ul>
        <li>Option 1</li>
        <li>Option 2</li>
        <li>Option 3</li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      x: 0,
      y: 0,
    };
  },
  mounted() {
    // Get the element to add the context menu to

    var myDiv = document.getElementById("myDiv");

    // Add an event listener for long-press events
    var pressTimer;
    myDiv.addEventListener("touchstart", function (e) {
      // Start the timer
      pressTimer = setTimeout(function () {
        // Show the custom context menu
        var myMenu = document.getElementById("myMenu");
        myMenu.style.display = "block";
        myMenu.style.left = e.touches[0].clientX + "px";
        myMenu.style.top = e.touches[0].clientY + "px";
      }, 100); // Long-press time in milliseconds
    });

    // Cancel the timer when the user releases the touch
    myDiv.addEventListener("touchend", function (e) {
      clearTimeout(pressTimer);
    });

    // Hide the context menu when the user clicks outside of it
    document.addEventListener("touchstart", function (e) {
      var myMenu = document.getElementById("myMenu");
      if (
        myMenu.style.display === "block" &&
        e.target !== myMenu &&
        !myMenu.contains(e.target)
      ) {
        myMenu.style.display = "none";
      }
    });
  },
  methods: {},
};
</script>

<style scoped>
#myMenu {
  position: absolute;
  background-color: #fff;
  border: 1px solid #ccc;
  padding: 5px;
}

#myMenu ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

#myMenu ul li {
  padding: 5px;
  cursor: pointer;
  -webkit-user-select: none; /* Safari */
  -moz-user-select: none; /* Firefox */
  -ms-user-select: none; /* IE10+/Edge */
  user-select: none; /* Standard syntax */
}

#myMenu ul li:hover {
  background-color: #f0f0f0;
}

#myDiv {
  -webkit-user-select: none; /* Safari */
  -moz-user-select: none; /* Firefox */
  -ms-user-select: none; /* IE10+/Edge */
  user-select: none; /* Standard syntax */

  border: 1px solid black; /* add border */
  width: 200px; /* set width */
  height: 100px; /* set height */
  text-align: center; /* center text */
  display: flex; /* use flexbox */
  justify-content: center; /* center horizontally */
  align-items: center; /* center vertically */
}
</style>
