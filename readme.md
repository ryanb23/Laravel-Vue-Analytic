# Introduction

This project is to build a dynamic landing page optimization script (Javascript) that's used by online marketers (e.g. Google, Facebook) to dynamic change elements of the landing page based on query string parameters in the URL.

This Javascript will be on the client's landing page (copy & pasted by client) and before the page loads (must be efficient - not slow down page load), will read the identifier in the URL and make requests to the server to fetch information on which elements to change and what content to change with.

On the backend, the user is able to create testing rules by defining CSS selectors and values to change. For example (using jQuery syntax),

$('#headline') -> change value to "Data Integration Made Easy"
$('.cta-button') -> change value to "Start Free Trial"
$('p#intro') -> change value to "This is some introduction paragraph."

Let's assume this rule has an ID of #123. So when a user visits the URL, www.example.com/?rid=123, the script will lookup rule ID #123 and pull the information from the server on what changes to make.

Based on the example selectors and values we created above, the script will render those changes to the DOM. This needs to happen seamlessly before the DOM fully renders initially.

On the server side, the user is able to perform CRUD operations on the rules. This is where the script will get the data from API call.

My application is running Laravel 5.4 / PHP7 and the user models and authentication are all available.

## Before You Begin

Make sure you have following installed:

1. Laravel 5.4
2. PHP 7
3. Postgres
4. [Composer](https://getcomposer.org/)