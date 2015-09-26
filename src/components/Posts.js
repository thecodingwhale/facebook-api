import React from 'react';
import request from 'superagent';
import Promise from 'promise';

export default React.createClass({
    getInitialState() {
        return {
            users: [{
                name: "Name",
                role: "Role"
            }]
        };
    },
    componentDidMount() {
        let promise = new Promise((resolve, reject) => {
            request
               .get('/users')
               .end((err, res) => {
                    if (err) {
                        reject(err);
                    } else {
                        this.setState({
                            users: res.body
                        });
                        resolve(res);
                    }
               });
        });
    },
    render() {
        var userList = this.state.users.map((user) => {
            return<li>
                Name: {user.name} <br />
                Role: {user.role}
            </li>
        });
        return (
            <div className="container">
                <ul>{userList}</ul>
            </div>
        );
    }
});
