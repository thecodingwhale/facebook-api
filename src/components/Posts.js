import React from 'react';
import request from 'superagent';
import Promise from 'promise';

export default React.createClass({
    getInitialState() {
        return {
            posts: []
        };
    },
    componentDidMount() {
        let promise = new Promise((resolve, reject) => {
            request
               .get('/facebook/posts')
               .end((err, res) => {
                    if (err) {
                        reject(err);
                    } else {
                        this.setState({
                            posts: res.body
                        });
                        resolve(res);
                    }
               });
        });
    },
    render() {
        var postList = this.state.posts.map((post) => {
            return<div className="well post" key={post.id}>
                <div className="post-title">Title: <strong>{post.title}</strong></div>
                <div className="post-description">Description: <em>{post.description}</em></div>
                <div className="post-date">{post.date}</div>
            </div>
        });        

        var classPosts = 'posts hide';
        if (Object.keys(this.state.posts).length > 1) {
            classPosts += 'posts';
        }        

        var classWell = 'well well-sm';
        if (Object.keys(this.state.posts).length > 1) {
            classWell += 'well well-sm hide';
        }      

        return (            
            <div>                
                <div id="ajax-spinner" className={classWell}>
                    <i className="fa fa-spinner fa-spin fa-lg"></i> Getting All Posts
                </div>
                <div className={classPosts}>
                    {postList}
                </div>
            </div>
        );
    }
});
