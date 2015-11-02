import React from 'react';
import request from 'superagent';
import Promise from 'promise';
import Infinite from 'react-infinite';

var ListItem = React.createClass({
    render() {
        var post = this.props.post;
        return <div className="well post">
            <div className="post-message">{post.message}</div>
            <div className="post-description">{post.description}</div>
            <div className="post-link">{post.link}</div>
            <div className="post-created-time">{post.created_time}</div>
        </div>;
    }
});

export default React.createClass({
    getPosts(page, callback) {
        let promise = new Promise((resolve, reject) => {
            request
               .get('/post/getPosts/' + page)
               .end((err, res) => {
                    if (err) {
                        reject(err);
                    } else {
                        callback(res.body);
                        resolve(res);
                    }
               });
        });
    },
    componentDidMount() {
        this.getListPosts();
    },
    getListPosts() {
        var self = this;
        this.setState({
            isInfiniteLoading: true
        });
        self.getPosts(this.state.page, (posts) => {
            self.setState({
                isInfiniteLoading: false,
                page: self.state.page += 1,
                elements: self.state.elements.concat(self.prepareListPost(posts))
            });
        });
    },
    getInitialState() {
        return {
            elements: [],
            page: 1,
            isInfiniteLoading: false
        };
    },
    prepareListPost(posts) {
        var elements = [];
        posts.map((post) => {
            elements.push(<ListItem key={post.id} post={post}/>)
        });
        return elements;
    },
    handleInfiniteLoad() {
        this.getListPosts();
    },
    ajaxLoader() {
        return <div id="ajax-spinner" className="well">
            <i className="fa fa-spinner fa-spin fa-lg"></i> Getting All Posts
        </div>;
    },
    render() {
        return <Infinite    elementHeight={160}
                            useWindowAsScrollContainer={true}
                            infiniteLoadBeginBottomOffset={50}
                            onInfiniteLoad={this.handleInfiniteLoad}
                            loadingSpinnerDelegate={this.ajaxLoader()}
                            isInfiniteLoading={this.state.isInfiniteLoading}
                            >
            {this.state.elements}
        </Infinite>;
    }
});