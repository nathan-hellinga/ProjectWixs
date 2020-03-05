// Dependencies
import React, { Component } from "react";
import { Container } from "react-bootstrap";
import { Redirect } from "react-router-dom";

// CSS/SASS
import "./sass/DashboardPage.scss";

/**
 * Purpose: This is a file containing...
 */
export default class DashboardPage extends Component {
  constructor(props) {
    super(props);

    this.state = {
      loggedIn: false
    };

    // Binds React class component methods
    this.setCookie = this.setCookie.bind(this);
    this.getCookie = this.getCookie.bind(this);
    this.eraseCookie = this.eraseCookie.bind(this);
  }

  // TODO: possible creation of a cookie class that can be referenced from each file instead of copy-pasting each function
  /**
   * Allows for the creation of a cookie
   *
   * @param {*} name the name of the cookie to be created
   * @param {*} value the value for which the cookie will contain
   * @param {*} days the number of days until the cookie expires
   */
  setCookie(name, value, days) {
    var expires = "";
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
    console.log("Cookie created");
  }

  /**
   * Allows for the retrieval of a cookie based on name
   *
   * @param {*} name
   */
  getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == " ") c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  }

  /**
   * Allows for the deletion of a cookie
   *
   * @param {*} name the name of the cookie to be deleted
   */
  eraseCookie(name) {
    document.cookie = name + "=; Max-Age=-99999999;";
  }

  /**
   * Checks if the user is currently logged in
   */
  componentDidMount() {
    var currentUser = this.getCookie("user");
    var currentSession = this.getCookie("usid");

    if (currentUser && currentSession) {
      this.setState({
        loggedIn: true
      });
    }
  }

  render() {
    return (
      <div>
        <Container>
          <div className="word-content">
            <h1>Dashboard --</h1>
          </div>
        </Container>
      </div>
    );
  }
}
