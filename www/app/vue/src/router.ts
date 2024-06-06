import TasksList from './components/TasksList.vue';
import TaskItem from './components/TaskItem.vue';
import TaskForm from './components/TaskForm.vue';

import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            name: 'tasks',
            path: '/',
            component: TasksList,
        },
        {
            name: 'task-form',
            path: '/task/edit/:taskId?',
            component: TaskForm,
            props: true,
            alias: '/task/add'
        },
        {
            name: 'task-item',
            path: '/task/:taskId(\\d+)',
            component: TaskItem,
            props: true
        },
    ],
});

export default router;