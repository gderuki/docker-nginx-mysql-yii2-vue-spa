import axios from 'axios';

const axiosClient = axios.create({
    baseURL: 'http://localhost/',
    responseType: 'json',
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
    },
});

export default {
    helpGet: (url: string) => axiosClient.get(url).then(res => res.data),
    helpPost: (url: string, data: any) => axiosClient.post(url, data).then(res => res.data),
    helpPatch: (url: string, data: any) => axiosClient.patch(url, data).then(res => res.data),
    helpDelete: (url: string) => axiosClient.delete(url)
}