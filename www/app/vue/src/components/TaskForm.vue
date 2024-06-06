<template>
    <a-card hoverable style='width: 100%' :loading='loading'>
        <a-form :model='task' :label-col='labelCol' :wrapper-col='wrapperCol' :rules='rules'>
            <a-form-item label='Task title' v-bind='validationErrors.title'>
                <a-input v-model:value='task.title' @blur='task.touched.title = true' />
            </a-form-item>
            <a-form-item label='Description' v-bind='validationErrors.description'>
                <a-input v-model:value='task.description' @blur='task.touched.description = true' />
            </a-form-item>
            <a-form-item label='Status' v-bind='validationErrors.status'>
                <a-input v-model:value='task.status' @blur='task.touched.status = true' />
            </a-form-item>
            <a-form-item :wrapper-col='{ span: 14, offset: 4 }'>
                <a-button size='large' type='primary' @click='handleSubmit'>
                    {{ isEdit ? 'Update' : 'Create' }}
                </a-button>
                <a-button size='large' style='margin-left: 10px' @click='resetFields' v-if='!isEdit'>
                    Reset
                </a-button>
                <a-button size='large' style='margin-left: 10px' @click='showAllTasks' danger>
                    Cancel
                </a-button>
            </a-form-item>
        </a-form>
    </a-card>
</template>

<script lang="ts">
import { reactive, computed, ref, onMounted, Ref } from 'vue';
import api from '@/api';
import { useRouter } from 'vue-router';
import Form from 'ant-design-vue/lib/form/Form';
import { Task } from '@/types';

const { useForm } = Form;

interface Rule {
    required?: boolean;
    message: string;
    trigger: string;
    // eslint-disable-next-line no-unused-vars
    // validator?: (_: any, value: any, callback: any) => void;
}

interface Rules {
    title: Rule[];
    description: Rule[];
    status: Rule[];
}

export default {
    props: {
        taskId: String,
    },

    setup(props: any) {
        let task: Task = reactive({
            id: '',
            title: '',
            description: '',
            status: 0,
            touched: {
                title: false,
                description: false,
                status: false,
            },
        });

        const getTrigger = (field: keyof typeof task.touched) => task.touched[field] ? 'blur' : 'change';

        const rules: Ref<Rules> = computed(() => ({
            title: [
                {
                    required: true,
                    message: 'Please provide task title',
                    trigger: getTrigger('title')
                },
            ],
            description: [
                {
                    required: true,
                    message: 'Please provide task description',
                    trigger: getTrigger('description')
                },
            ],
            status: [
                {
                    required: true,
                    message: 'Please provide task status',
                    trigger: getTrigger('status')
                },
            ],
        }));

        const {
            resetFields,
            validate,
            validateInfos: validationErrors
        } = useForm(task, rules);

        const handleSubmit = () => {
            validate()
                .then(
                    async () => {
                        const { taskId } = props;
                        const updatedTask = taskId ?
                            await api.helpPatch(`tasks/${taskId}`, task) :
                            await api.helpPost('tasks', task);
                        Object.assign(task, updatedTask);
                        router.push({ name: 'task-item', params: { taskId: task.id } });
                        router.push({ name: 'tasks' });
                    }
                )
                .catch(() => {
                    console.log('error');
                });
        }

        const isEdit = computed(() => !!props.taskId);
        const loading = ref(!!props.taskId);

        const loadTask = async () => {
            Object.assign(task, await api.helpGet(`tasks/${props.taskId}`));
            loading.value = false;
        };

        const router = useRouter();

        const showAllTasks = () => {
            router.push({ name: 'tasks' });
        };

        onMounted(async () => {
            if (isEdit.value) {
                await loadTask();
            }
        });

        return {
            ...props,
            resetFields,
            validationErrors,
            task,
            handleSubmit,
            rules,
            isEdit,
            loading,
            loadTask,
            showAllTasks
        };
    },
    data(): any {
        return {
            labelCol: { span: 4 },
            wrapperCol: { span: 14 },
        }
    },
};
</script>