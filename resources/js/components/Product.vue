<template>
<div class="body">
    <div class="app">
        <!--詳細表示-->
        <div class="row">
            <!--店画像-->
            <div class="col-xs-4 col-lg-4">
                <div id="photo">
                    <img v-bind:src="f_photo"><br>
                </div>
                <div id="name">
                    <p>{{ shop_name }}</p>
                    <span class="pan_space"> URL：<a v-bind:href="o_url" target="_blank">{{ shop_name }}の公式</a></span>
                </div>
            </div>
            <!--店詳細-->
            <div class="col-xs-8 col-lg-8">
                <div v-if="!hisname">
                    <span class="pan_name">{{ f_name }}</span> 
                    <span class="pan_space">></span>
                    <span class="pan_name">{{ s_name }}</span><br>
                    <!--非ログインとログイン-->
                    <div v-if="isActive">
                        <!--alart用ボタン-->
                        <button id="hashigo_save" v-on:click="alert" v-bind:disabled="alert_click">はしご保存</button>
                        <button id="hashigo_save" type="submit" @click="currentsearch">現在地</button>
                    </div>
                    <div v-else>
                        <button id="hashigo_save" v-bind:disabled="insertClick" v-on:click="insertList(f_id,s_id,userid)">はしご保存</button>
                        <button id="hashigo_save" type="submit" @click="currentsearch">現在地</button>
                    </div>
                </div>
                <div v-else>
                    <span class="pan_name">{{ f_name }}</span> 
                    <span class="pan_space">></span>
                    <span class="pan_name">{{ second_name }}</span>
                    <span class="pan_space">></span>
                    <span class="pan_name">{{ s_name }}</span><br>
                    <div v-if="isActive"></div>
                    <div v-else>
                        <button id="hashigo_save" v-bind:disabled="insertClick" v-on:click="t_save(s_id,listid)">三軒目保存</button>
                    </div>
                </div>
                <br>
                <div id="shop_info">
                    <p>住所：{{ tel_add }}</p><hr>
                    <p>営業時間：{{ time }}</p><hr>
                    <p>飲み放題：{{ drink }}</p><hr>
                    <p>食べ放題：{{ eat }}</p><hr>
                    <p>総席数：{{ capa }} 　/　禁煙席：{{ smoking }}　/　クレジット：{{ credit }}</p><hr>
                    <p>アクセス：{{ access }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-9 col-lg-9">
                <div id="map">
                    <GmapMap :center="center" :zoom="zoom" style="width: 100%; height: 100%;" ref="map">
                        <GmapMarker  v-for="(m,id) in marker_items"
                            :position="m.position"
                            :title="m.title"
                            :url="m.url"
                            :icon="m.icon"
                            :clickable="true" :draggable="false" :key="id" @click="clickMarker(id)" ref ="icon">
                        </GmapMarker>
                    </GmapMap>
                </div>
            </div>
            <div class="col-xs-3 col-lg-3" >
                <div id="products">
                    <table>
                    <tr v-for="(s,id) in marker_items" :key="id">
                        <div v-if="id >= 2">
                            <img v-bind:src="s.photo"><img><br>
                            <button  id="detail" v-on:click="s_click(id)">{{ s.title }}</button><hr>
                        </div>
                    </tr>
                </table>
                </div>
            </div>
        </div>
            <span class="hot_text">Powered by <a href="http://webservice.recruit.co.jp/">ホットペッパー Webサービス</a></span>
            <span class="hot_text">画像提供：ホットペッパー グルメ</span>

            <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.4/css/all.css">
            <div id="page_top"><a href="#"></a></div>
    </div>
</div>
</template> 
<script>
import { METHODS } from 'http';
import axios from 'axios';

export default {
    props:{
        product:String,
        place:String,
        arr:[],
        userid:String,
        listid:String,
        hisname: String,
    },

    data () {
        return {
            name: "",
            url: "",
            photo: "",
            map:{},
            geocode:{},
            address: '',
            center: {lat: 36.71, lng: 139.72},
            zoom: 18,
            marker_items: [],
            icon: {url: "", scaledSize:"", scaledColor: ""},
            id:"",
            lat:"",
            lng:"",
            //送る情報
            detail:"/detail?id=",
            f_lat:"&lat=",
            f_lng:"&lng=",
            //店詳細
            f_photo:"",
            shop_name:"",
            tel_add:"",
            time:"",
            capa:"",
            credit:"",
            smoking:"",
            dirnk:"",
            eat:"",
            access:"",
            //一件目
            f_name:"",
            //二件目と三件目
            s_name:"",
            //URL＆アイコン関連
            o_url:"",
            b_id:null,
            //postするid
            f_id:"",
            s_id:"",
            t_id:"",
            //ボタンのクリックアクション
            position_id:0,
            insertClick:true,
            isActive:true,
            alert_click:false,
            second_name:"",
             //現在地の情報
            current:[],
        }
    },

    //1件目の詳細
    mounted (){
        let json = JSON.parse(this.product)
        this.center = {lat:parseFloat(json[0].lat), lng:parseFloat(json[0].lng)}
        this.setcentermarker(parseFloat(json[0].lat),parseFloat(json[0].lng))
        this.setshopmarker(JSON.parse(this.place))
        this.f_photo = json[0].photo.pc.l
        this.shop_name = json[0].name
        //２件目
        this.second_name= json[0].name
        this.tel_add = json[0].address
        this.time = json[0].open
        this.capa = json[0].capacity
        this.credit = json[0].card
        this.drink = json[0].free_drink
        this.eat = json[0].free_food
        this.smoking = json[0].non_smoking
        this.access = json[0].access
        this.o_url = json[0].urls.pc
        //パンくずリスト一件目（固定）
        if(!this.hisname){
            this.f_name = json[0].name
        }else{
            this.f_name = this.hisname
        }
        //ボタンの可視化
        this.f_id =json[0].id
        if(this.userid !== ""){
            this.isActive = false
        }
    },

    methods: {
         //現在地取得
        currentPosition () {
            return new Promise(function(resolve,reject){
                navigator.geolocation.getCurrentPosition((position)=>{resolve(position.coords)})
            })
        },
        //マーカーアイテム２を作り現在地にピンを立てる
        setcentermarker(lat,lng){
            this.current=[];
            this.$refs.map.panTo({lat: lat, lng: lng})
            this.current.push({
                position: {lat: lat, lng: lng}, 
                title: '現在地', 
                icon: {url:'http://pictogram2.com/p/p0957/3.png',scaledSize:{width:70,height:75} ,scaledColor:'#0000'}})
        },

        // 現在位置取得
        async currentsearch(){
            let position = await this.currentPosition()
            let lat = position.latitude
            let lng = position.longitude
            this.setcentermarker(lat,lng)
        },

        // キーワード位置取得
        keywordPosition () {
            return new Promise((resolve,reject)=>{
                this.geocoder = new google.maps.Geocoder();
                this.geocoder.geocode({'address': this.address},(results, status)=>{resolve(results[0].geometry.location)})
            })
         },

        //ピン立て 中央
        setcentermarker(lat,lng){
            this.$refs.map.panTo({lat: lat, lng: lng})
            //2軒目と3軒目で、現在地のicon変更
            if(!this.hisname){
                this.marker_items.push({position: {lat: lat, lng: lng}, title: '現在地', icon: {url: 'http://pictogram2.com/p/p0957/3.png',scaledSize:{width:70,height:75} ,scaledColor: '#0000'}})
            }else{
                this.marker_items.push({position: {lat: lat, lng: lng}, title: '現在地', icon: {url: 'http://pictogram2.com/p/p0957/3.png',scaledSize:{width:70,height:75} ,scaledColor: '#0000'}})
            }
        },

        // hotpepperから店情報取得
        getList(lat,lng){
            return axios.post('/api/list',{
                lng: lng,
                lat: lat
            }).then((res)=>{
                return res.data
            })
        },

        // shoplistピン立て
        setshopmarker(shoplist){
            shoplist.map((shopdata)=>{
            let name = shopdata.name
            let url = shopdata.urls.pc
            let photo = shopdata.photo.pc.l
            let lat = shopdata.lat
            let lng = shopdata.lng
            //店、詳細情報
            this.marker_items.push({position: {lat: parseFloat(lat), lng: parseFloat(lng)}, title: name, url: url, photo: photo,
                address:shopdata.address, open:shopdata.open, capacity:shopdata.capacity, card:shopdata.card, id:shopdata.id,
                non_smoking:shopdata.non_smoking, free_drink:shopdata.free_drink, free_food:shopdata.free_food, access:shopdata.access,
                icon: {url: 'http://maps.google.co.jp/mapfiles/ms/icons/red-dot.png',scaledSize:{width:40,height:40} ,scaledColor: '#0000'}
                , button:false})
            });
        },

        //マーカーの表示内容
        clickMarker(id){
           this.name = this.marker_items[id].title
           this.url = this.marker_items[id].url
           this.photo = this.marker_items[id].photo
           this.id =this.marker_items[id].id
           this.lat =this.marker_items[id].position.lat
           this.lng =this.marker_items[id].position.lng
        },
        
        //非ログイン時のボタン
        alert(){
            alert('ログインしてください')
            this.alert_click = true
        },

        //2件目
        s_click(id){
            this.shop_name = this.marker_items[id].title
            this.f_photo = this.marker_items[id].photo
            this.o_url = this.marker_items[id].url
            this.tel_add = this.marker_items[id].address
            this.time = this.marker_items[id].open
            this.capa = this.marker_items[id].capacity
            this.credit = this.marker_items[id].card
            this.dirnk = this.marker_items[id].free_dirnk
            this.eat = this.marker_items[id].free_food
            this.smoking = this.marker_items[id].non_smoking
            this.access = this.marker_items[id].access
            this.s_name = this.marker_items[id].title
            this.s_id = this.marker_items[id].id
            
            //ボタンの押す押せない
            this.position_id = id
            this.insertClick = this.marker_items[id].button

            //2件目、マーカー色チェンジ
            if(this.b_id !== null){
                this.$refs.icon[this.b_id].$markerObject.icon.url = 'http://maps.google.co.jp/mapfiles/ms/icons/red-dot.png'
            }
            this.$refs.icon[id].$markerObject.icon.url = 'http://maps.google.co.jp/mapfiles/ms/icons/ltblue-dot.png'
            this.$refs.map.panTo({lat: this.marker_items[id].position.lat, lng: this.marker_items[id].position.lng})
            this.b_id = id
        },

        //保存ボタン
        insertList(f_id, s_id,userid){
            //ボタンを連続で押せなくする
            this.marker_items[this.position_id].button = true
            this.insertClick=true
            alert(this.f_name + 'と' + this.s_name + 'を保存しました')
            //非同期通信
            return axios.post('/api/insert',{
                f_id:f_id,
                s_id:s_id,
                userid:userid,
            }).then((res)=>{
                console.log(res.data);
                return res.data
            })
        },
        //3軒目保存
         t_save(s_id,listid){
            //ボタンを連続で押せなくする
            this.marker_items[this.position_id].button = true
            this.insertClick=true
            alert(this.s_name + 'を3軒目に追加')
            //非同期通信
            return axios.post('/api/update',{
                s_id:s_id,
                listid:listid,
               
            }).then((res)=>{
                console.log(res.data);
                return res.data
            })
        }
    }

}
</script>

<style scoped>
button{
        background-color: transparent;
        border: none;
        cursor: pointer;
        outline: none;
        padding: 0;
        appearance: none;
}
.body {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    z-index: -1;
}
.app {
    padding:0px 50px;
    margin-top: 60px;
    width: 100%;
}
#map {
    width: 100%;
    height: 480px;
} 
.col-lg-4 {
    /* 左上の写真・店名の設定 */
    width: 100%;
    height: 350px;
    text-align: center;
    border-radius: 3px;
    background-color:  rgba(255, 255, 255, 0.788);
    color: rgb(0, 0, 0);
    letter-spacing: 2px;
    margin: 10px auto;
    padding: 10px;
    white-space: pre-line;
}
.col-lg-4 p {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: -5px;
}
#photo img {
    border: 4px #ff0000 solid;
}
#name {
    height: 76px;
    display: block;
    font-size: 16px;
    font-weight: bold;
    margin-top: 10px;
    padding: 0 0 10px 0;
    overflow-y: scroll;
    white-space: pre-line;
}
.col-lg-8 {
    width: 100%;
    height: 350px;
    border-radius: 3px;
    background-color: rgba(255, 255, 255, 0.788);
    color: rgb(0, 0, 0);
    font-size: 18px;
    font-weight: bold;
    letter-spacing: 2px;
    margin-top: 10px;
    white-space: pre-line;
}
.col-lg-8 hr {
    background-color: #000;
}
.pan_name{
    white-space: nowrap;
    margin-top: 20px;
    max-width: 180px;
    min-width: 100px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
    font-weight: bold;
}
.pan_space {
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
    font-weight: bold;
}
.pan_space a {
    color: rgb(0, 47, 255);
    font-weight: bold;
}
#hashigo_save {
    white-space: nowrap;
    display: inline-block;
    padding: 0.5em 1em;
    text-decoration: none;
    background: orangered;/*ボタン色*/
    color: rgb(255, 255, 255);
    transition: background-color 0.5s;
}
#hashigo_save:hover {
    background-color: orange;
    transition: 1s;
}
#hashigo_save:disabled {
    white-space: nowrap;
    display: inline-block;
    padding: 0.5em 1em;
    text-decoration: none;
    background: grey;/*ボタン色*/
    color: rgb(255, 255, 255);
}
#shop_info {
    width: 100%;
    height: 200px;
    margin: -10px;
    padding: 30px;
    overflow-y: scroll; 
}
.col-lg-9 {
    position: relative;
    padding: 5px 5px 15px 5px;
    width: 90%; /* ボックス幅 */
    height: 100%;
    background-color: #fffff9; /* ボックス背景色 */
    color: #000; /* 文章色 */
    border: 5px solid orangered; /* 枠線 */
    border-radius: 3px; /* 角の丸み */
    box-shadow:  0 0 8px rgb(126, 126, 126), 0 0 2px rgb(150, 150, 150) inset;
}
.col-lg-9:before, .col-lg-9:after {
    position: absolute;
    content: '';
    width: 25px; 
    bottom: 3px;
    border-radius: 2px;
    box-shadow: 1px 1px 3px #666;
}
.col-lg-9:before {
    right: 55px;
    border: solid 3px #333333; /*飾ペン黒 */
}
.col-lg-9:after {
    right: 20px;
    border: solid 3px #ff42a0; /*飾ペンピンク */
    transform: rotate(8deg); /*飾ペン角度 */
}
.col-lg-3 {
    position: relative;
}
#products {
    text-align: center;
    position: relative;
    background-color:  rgba(255, 255, 255, 0.788);
    /* padding: 1em; */
    border-top: 10px solid orangered;
    border-bottom: 10px solid orangered;
    padding-top: 30px;
    padding-left: 30px;
    margin-left: 10%;
    width: 300px;
    height: 510px;
    overflow-y: scroll;
}
::-webkit-scrollbar {
    width: 5px;
}
::-webkit-scrollbar-track {
    border-radius:  30px;
    background: #eee;
}
::-webkit-scrollbar-thumb {
    border-radius: 30px;
    background: #ff0000;
}
#products hr {
    background-color: #FFF;
}
#detail {
    margin-top: 10px;
    max-width: 200px;
    min-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: inline-block;
    padding: 0.5em 1em;
    text-decoration: none;
    background: orangered;/*ボタン色*/
    color: rgb(255, 255, 255);
    transition: background-color 0.5s;
}
#detail:hover {
    background-color: orange;
    transition: background-color 1s;
}

.hot_text {
  margin-top: 0%;
  font-size: 0.3rem;
}


/* スマホ対応 */
@media screen and (max-width: 781px){
    .app {
        padding:0px 20px;
        margin-top: 60px;
        width: 100%;
    }

    .col-xs-4 {
        /* 左上の写真・店名の設定 */
        width: 100%;
        height: 350px;
        text-align: center;
        border-radius: 3px;
        color: rgb(0, 0, 0);
        letter-spacing: 2px;
        margin: 0 auto;
        padding: 10px;
        white-space: pre-line;
    }

    .col-xs-4 p {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: -5px;
    }

    .col-xs-8 {
        width: 100%;
        height: 350px;
        border-radius: 3px;
        color: rgb(0, 0, 0);
        font-size: 18px;
        font-weight: bold;
        letter-spacing: 2px;
        margin: -10px auto;
        white-space: pre-line;
    }
    .col-xs-8 hr {
        background-color: #000;
    }
    .col-xs-8 .pan_name{
        white-space: nowrap;
        margin-top: 20px;
        max-width: 100px;
        min-width: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        font-size: 12px;
        font-weight: bold;
    }
    .col-xs-8 .pan_space {
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        font-weight: bold;
    }
    .col-xs-8 .pan_space a {
        color: rgb(0, 47, 255);
        font-weight: bold;
    }
    .col-xs-8 #hashigo_save {
        white-space: nowrap;
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: orangered;/*ボタン色*/
        color: rgb(255, 255, 255);
        font-size: 14px;
    }
    .col-xs-8 #hashigo_save:disabled {
        white-space: nowrap;
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: grey;/*ボタン色*/
        color: rgb(255, 255, 255);
    }

    .col-xs-9 {
        margin: 10px;
        margin-top: 20px;
        position: relative;
        padding: 5px 5px 15px 5px;
        width: 100%; /* ボックス幅 */
        height: 350px;
        background-color: #fffff9; /* ボックス背景色 */
        color: #000; /* 文章色 */
        border: 5px solid orangered; /* 枠線 */
        border-radius: 3px; /* 角の丸み */
        box-shadow:  0 0 8px rgb(126, 126, 126), 0 0 2px rgb(150, 150, 150) inset;
    }
    .col-xs-9:before, .col-xs-9:after {
        position: absolute;
        content: '';
        width: 25px; 
        bottom: 3px;
        border-radius: 2px;
        box-shadow: 1px 1px 3px #666;
    }
    .col-xs-9:before {
        right: 55px;
        border: solid 3px #333333; /*飾ペン黒 */
    }
    .col-xs-9:after {
        right: 20px;
        border: solid 3px #ff42a0; /*飾ペンピンク */
        transform: rotate(8deg); /*飾ペン角度 */
    }
    .col-xs-9 #map {
        width: 100%;
        height: 320px;
    }
    .col-xs-3 {
        position: relative;
        width: 100%;
    }
    .col-xs-3 #products {
        text-align: center;
        position: relative;
        background-color: #ffffffa9;
        border-top: 10px solid orangered;
        border-bottom: 10px solid orangered;
        padding: 30px;
        margin-top: 10px;
        margin-left: 5px;
        width: 100%;
        height: 550px;
        overflow-y: scroll;
    }
    #page_top{
        width: 50px;
        height: 50px;
        position: fixed;
        right: 0;
        bottom: 0;
        background: #3fe0ef;
        opacity: 0.6;
    }
    #page_top a{
        position: relative;
        display: block;
        width: 50px;
        height: 50px;
        text-decoration: none;
    }
    #page_top a::before{
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        content: '\f106';
        font-size: 25px;
        color: #fff;
        position: absolute;
        width: 25px;
        height: 25px;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        margin: auto;
        text-align: center;
    }
}
</style>