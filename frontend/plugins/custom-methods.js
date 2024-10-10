export default ({ app }, inject) => {

  inject("utils", {
    add_zeros(number) {
      return number && number.toString().padStart(4, '0');

    },
    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2) + ".00".replace(".00.00", ".00");
      } else {
        return n + ".00".replace(".00.00", ".00");

      }
    },
    getSum(values) {
      return values.reduce((acc, curr) => acc + curr, 0);
    },
    currency_format(n, type = "₹") {
      if (type == "₹") {
        return parseFloat(n).toLocaleString('en-IN', { style: 'currency', currency: 'INR' });
      }

      return parseFloat(n).toLocaleString('en-US', { style: 'currency', currency: 'USD' });
    },
    numberToWords(num) {
      const belowTwenty = [
        'Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
        'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
      ];
      const tens = [
        '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
      ];
      const thousands = ['', 'Thousand', 'Million', 'Billion'];

      if (num === 0) return 'Zero';

      let words = '';

      function helper(n) {
        if (n === 0) return '';
        else if (n < 20) return belowTwenty[n] + ' ';
        else if (n < 100) return tens[Math.floor(n / 10)] + ' ' + helper(n % 10);
        else return belowTwenty[Math.floor(n / 100)] + ' Hundred ' + helper(n % 100);
      }

      let i = 0;
      while (num > 0) {
        if (num % 1000 !== 0) {
          words = helper(num % 1000) + thousands[i] + ' ' + words;
        }
        num = Math.floor(num / 1000);
        i++;
      }

      return `(${words.trim()} Only)`;
    },
    full_name({ title, first_name, last_name }) {
      return `${title} ${first_name} ${last_name}`;
    },
    full_address({ city, state, country }) {
      const formatValue = (value) => value && value !== "null" ? value : "---";
      return `${formatValue(city)} ${formatValue(state)} ${formatValue(country)}`;
    },
    getRelatedClass(status_id) {
      let status = {
        0: "available",
        1: "booked",
        2: "occupied",
        3: "checked_out",
        4: "blocked",
      };

      return status[status_id || ""];
    },
    capsTitle(val) {
      if (val == "gst") {
        val = val.toUpperCase();
        return val;
      }
      let res = val;
      let r = res.replace(/[^a-z]/g, " ");
      let title = r.replace(/\b\w/g, (c) => c.toUpperCase());
      return title;
    },
    open_external_link(link) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", link);
      document.body.appendChild(element);
      console.log(element);
      element.click();
    },
  });

  inject("dateFormat", {
    dmyhm() {
      const date = new Date(); // Current date and time
      const day = String(date.getDate()).padStart(2, '0');

      // Get month abbreviation
      const monthNames = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
      const month = monthNames[date.getMonth()];

      const year = date.getFullYear();
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      return `${day} ${month} ${year} ${hours}:${minutes}`;
    },

    getMyDayOnly(originalData) {
      const date = new Date(originalData);
      const day = date.getDay(); // Returns the day of the week as a number (0 for Sunday, 1 for Monday, etc.)
      const daysOfWeek = [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
      ];
      return daysOfWeek[day];
    },


    dmy(date) {
      let dateObj = new Date(date);

      let day = dateObj.getDate().toString().padStart(2, "0");
      let month = dateObj
        .toLocaleString("en-GB", { month: "short" })
        .slice(0, 3);
      let year = dateObj.getFullYear();

      return `${day} ${month} ${year}`;
    },
    format1: (inputdate) => {
      // Create a Date object with the date "2023-09-13"  Output Sun, Jan 01, 2023
      const inputDate = new Date(inputdate);
      const options = {
        year: "numeric",
        month: "short",
        day: "2-digit",
        weekday: "short",
      };
      const formattedDate = inputDate.toLocaleDateString("en-US", options);
      return formattedDate;
    },
    format11: (inputdate) => {
      // Create a Date object with the provided date
      const inputDate = new Date(inputdate);

      // Specify the formatting options
      const options = {
        year: "numeric",
        month: "short",
        day: "2-digit",
      };

      // Format the date
      const formattedDate = inputDate
        .toLocaleDateString("en-GB", options)
        .replace(",", "");

      return formattedDate;
    },
    format2: (inputdate) => {
      // Create a Date object with the date "2023-09-13"  Output: "23-09-13"
      const date = new Date(inputdate);

      const year = date.getFullYear().toString().slice(-2);
      const month = (date.getMonth() + 1).toString().padStart(2, "0"); // Note: Month is zero-indexed
      const day = date.getDate().toString().padStart(2, "0");

      return `${year}-${month}-${day}`;
    },
    format3: (inputdate) => {
      const currentDate = new Date(inputdate);

      const year = currentDate.getFullYear();
      const month = (currentDate.getMonth() + 1).toString().padStart(2, "0"); // Adding 1 to month because it's zero-based.
      const day = currentDate.getDate().toString().padStart(2, "0");
      const hours = currentDate.getHours().toString().padStart(2, "0");
      const minutes = currentDate.getMinutes().toString().padStart(2, "0");
      const seconds = currentDate.getSeconds().toString().padStart(2, "0");

      return `${year}-${month}-${day} ${hours}:${minutes} `;
    },
    format4: (inputdate) => {
      if (
        inputdate == "---" ||
        inputdate == "--" ||
        inputdate == 0 ||
        inputdate == ""
      )
        return "---";

      const currentDate = new Date(inputdate); //Output Sun, Jan 01, 2023 10:20

      const year = currentDate.getFullYear();
      const month = (currentDate.getMonth() + 1).toString().padStart(2, "0"); // Adding 1 to month because it's zero-based.
      const day = currentDate.getDate().toString().padStart(2, "0");
      const hours = currentDate.getHours().toString().padStart(2, "0");
      const minutes = currentDate.getMinutes().toString().padStart(2, "0");
      const seconds = currentDate.getSeconds().toString().padStart(2, "0");

      const inputDate = new Date(inputdate);
      const options = {
        year: "numeric",
        month: "short",
        day: "2-digit",
        weekday: "short",
      };
      const formattedDate = inputDate.toLocaleDateString("en-US", options);
      if (year == 1970 || inputdate == 0) return "---";
      return `${formattedDate}  ${hours}:${minutes} `;
    },
    format5: (inputdate) => {
      const currentDate = new Date(inputdate);

      const year = currentDate.getFullYear();
      const month = (currentDate.getMonth() + 1).toString().padStart(2, "0"); // Adding 1 to month because it's zero-based.
      const day = currentDate.getDate().toString().padStart(2, "0");
      const hours = currentDate.getHours().toString().padStart(2, "0");
      const minutes = currentDate.getMinutes().toString().padStart(2, "0");
      const seconds = currentDate.getSeconds().toString().padStart(2, "0");

      const inputDate = new Date(inputdate);
      const options = {
        year: "numeric",
        month: "short",
        day: "2-digit",
        weekday: "short",
      };
      const formattedDate = inputDate.toLocaleDateString("en-US", options);

      return `${hours}:${minutes} ${formattedDate}   `;
    },
    format6: (inputdate) => {
      //20:30
      if (
        inputdate == "---" ||
        inputdate == "--" ||
        inputdate == 0 ||
        inputdate == ""
      )
        return "---";
      const currentDate = new Date(inputdate);

      const year = currentDate.getFullYear();
      const month = (currentDate.getMonth() + 1).toString().padStart(2, "0"); // Adding 1 to month because it's zero-based.
      const day = currentDate.getDate().toString().padStart(2, "0");
      const hours = currentDate.getHours().toString().padStart(2, "0");
      const minutes = currentDate.getMinutes().toString().padStart(2, "0");
      const seconds = currentDate.getSeconds().toString().padStart(2, "0");

      const inputDate = new Date(inputdate);
      const options = {
        year: "numeric",
        month: "short",
        day: "2-digit",
        weekday: "short",
      };
      const formattedDate = inputDate.toLocaleDateString("en-US", options);
      if (year == 1970 || inputdate == 0) return "---";
      return `${hours}:${minutes}    `;
    },
    monthStartEnd: (inputdate) => {
      // Get the current date
      const currentDate = new Date(inputdate);

      // Get the first day of the current month
      const firstDayOfMonth = new Date(
        currentDate.getFullYear(),
        currentDate.getMonth(),
        1
      );

      // Get the last day of the current month
      const lastDayOfMonth = new Date(
        currentDate.getFullYear(),
        currentDate.getMonth() + 1,
        0
      );

      // Format the dates as strings (in 'YYYY-MM-DD' format)
      const formattedFirstDay = `${firstDayOfMonth.getFullYear()}-${(
        firstDayOfMonth.getMonth() + 1
      )
        .toString()
        .padStart(2, "0")}-01`;
      const formattedLastDay = `${lastDayOfMonth.getFullYear()}-${(
        lastDayOfMonth.getMonth() + 1
      )
        .toString()
        .padStart(2, "0")}-${lastDayOfMonth.getDate()}`;

      return { first: formattedFirstDay, last: formattedLastDay };
    },
    minutesToHHMM(minutes) {
      var hours = Math.floor(minutes / 60);
      var remainingMinutes = minutes % 60;
      return (
        (hours < 10 ? "0" : "") +
        hours +
        ":" +
        (remainingMinutes < 10 ? "0" : "") +
        remainingMinutes
      );
    },
    time2Hm: (inputdate) => {
      // Split the time string by ':'
      const timeParts = inputdate.split(":");

      // Extract the hour and minutes
      const hour = timeParts[0];
      const minutes = timeParts[1];

      // console.log(`Hour: ${hour}`);
      // console.log(`Minutes: ${minutes}`);
      return `${hour}:${minutes}`;
    },
    //Output: e.g., 02-04-2024, Monday

    format_date_with_dayname: (inputdate) => {
      // Create a new Date object for today's date
      var today = new Date(inputdate);

      //console.log("today", today);

      // Get the day, month, year, and day of the week
      var day = today.getDate();
      var month = today.getMonth() + 1; // Month is zero-based, so add 1
      var year = today.getFullYear();
      var dayOfWeek = today.getDay(); // 0 for Sunday, 1 for Monday, ..., 6 for Saturday

      //console.log("datodayyOfWeek", dayOfWeek);

      // Ensure day and month are formatted with leading zeros if needed
      day = (day < 10 ? "0" : "") + day;
      month = (month < 10 ? "0" : "") + month;

      // Array to map day of the week number to its name
      var daysOfWeek = [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
      ];

      // Get the name of the day of the week
      var dayName = daysOfWeek[dayOfWeek];

      // Construct the desired date format
      var formattedDate = day + "-" + month + "-" + year + ", " + dayName;
      return formattedDate;
      //console.log(formattedDate); // Output: e.g., 02-04-2024, Monday
    },
    format_month_name_year: (inputdate) => {
      // Create a Date object with the date "2023-09-13"  Output: "23-09-13"
      const date = new Date(inputdate);

      const months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
      ];

      const currentDate = date;
      const currentMonth = months[currentDate.getMonth()];
      const currentYear = currentDate.getFullYear();

      return `${currentMonth} ${currentYear}`;
    },

    can(per, thisobj) {
      let u = thisobj.$auth.user;

      return (
        (u && u.permissions.some((e) => e == per || per == "/")) ||
        u.is_master ||
        u.user_type == "branch"
      );
    },
  });

  inject("pagePermission", {
    can(per, thisobj) {
      let u = thisobj.$auth.user;

      // return (
      //   (u && u.permissions.some((e) => e == per || per == "/")) ||
      //   u.is_master ||
      //   u.user_type == "branch"
      // );

      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
  });
};
