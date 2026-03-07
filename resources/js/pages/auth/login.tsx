import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';
import { Head, useForm } from '@inertiajs/react'; // تم تغيير Form إلى useForm
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
    // 1. تعريف useForm لإدارة البيانات وحالة الإرسال
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    // 2. دالة الـ submit التي طلبتها مع إعدادات Vercel
    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('login'), {
            onFinish: () => reset('password'),
            forceFormData: true,  // مهم لضمان جودة الطلب في السيرفرات
            preserveScroll: true,
        });
    };

    return (
        <AuthLayout title="تسجيل الدخول إلى حسابك" description="أدخل بريدك الإلكتروني وكلمة المرور أدناه لتسجيل الدخول">
            <Head title="تسجيل الدخول" />

            {/* 3. ربط الدالة بحدث onSubmit في النموذج */}
            <form onSubmit={submit} className="flex flex-col gap-6 text-right" dir="rtl">
                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="email" className="text-right">البريد الإلكتروني</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            value={data.email}
                            required
                            autoFocus
                            tabIndex={1}
                            autoComplete="email"
                            placeholder="email@example.com"
                            className="text-right"
                            onChange={(e) => setData('email', e.target.value)}
                        />
                        <InputError message={errors.email} />
                    </div>

                    <div className="grid gap-2">
                        <div className="flex items-center justify-between">
                            <Label htmlFor="password">كلمة المرور</Label>
                            {canResetPassword && (
                                <TextLink href={route('password.request')} className="text-sm" tabIndex={5}>
                                    نسيت كلمة المرور؟
                                </TextLink>
                            )}
                        </div>
                        <Input
                            id="password"
                            type="password"
                            name="password"
                            value={data.password}
                            required
                            tabIndex={2}
                            autoComplete="current-password"
                            placeholder="كلمة المرور"
                            className="text-right"
                            onChange={(e) => setData('password', e.target.value)}
                        />
                        <InputError message={errors.password} />
                    </div>

                    <div className="flex items-center space-x-reverse space-x-3">
                        <Checkbox 
                            id="remember" 
                            name="remember" 
                            tabIndex={3} 
                            checked={data.remember}
                            onCheckedChange={(checked) => setData('remember', !!checked)}
                        />
                        <Label htmlFor="remember" className="pr-1">تذكرني</Label>
                    </div>

                    <Button type="submit" className="mt-4 w-full" tabIndex={4} disabled={processing}>
                        {processing && <LoaderCircle className="h-4 w-4 animate-spin ml-2" />}
                        تسجيل الدخول
                    </Button>
                </div>
            </form>

            {status && <div className="mb-4 text-center text-sm font-medium text-green-600">{status}</div>}
        </AuthLayout>
    );
}