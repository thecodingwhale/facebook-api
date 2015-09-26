import React from 'react';
import { Router, Route } from 'react-router';
import App from './components/App';
import PoweredBy from './components/Powered-by';
import About from './components/About';
import Posts from './components/Posts';

window.React = React;

React.render(
  <Router>
    <Route path="/" component={App}>
      <Route path="/about" component={About}/>
      <Route path="/poweredby" component={PoweredBy}/>
      <Route path="/posts" component={Posts}/>
    </Route>
  </Router>
  , document.getElementById('content')
);
