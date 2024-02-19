import axios from 'axios';

const instanceAxios = axios.create({
    baseURL: 'http://localhost/epicbeats/',
    withCredentials: true
});


export default instanceAxios;