// 接口部分
//基本的ajax访问后端接口类

//举报接口
function v1_complain(url, param, callBack) {
    ajaxRequest(url + "vote/api/complain", param, "post", callBack);
}

//投票接口
function v1_vote(url, param, callBack) {
    ajaxRequest(url + "vote/api/vote", param, "post", callBack);
}

//分享参赛选手
function v1_shareVoteUser(url, param, callBack) {
    ajaxRequest(url + "vote/api/shareVoteUser", param, "post", callBack);
}

//报名信息
function v1_apply(url, param, callBack) {
    ajaxRequest(url + "vote/api/apply", param, "post", callBack);
}

//关注信息
function v1_voteUser_guanzhu(url, param, callBack) {
    ajaxRequest(url + "vote/api/voteUser/guanzhu", param, "post", callBack);
}

//关注信息
function v1_vote_payOrder(url, param, callBack) {
    ajaxRequest(url + "vote/api/vote/payOrder", param, "post", callBack);
}

