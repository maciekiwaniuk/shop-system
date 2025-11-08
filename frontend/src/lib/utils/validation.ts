import { z } from 'zod';

export const loginSchema = z.object({
    email: z.string().email('Please enter a valid email address'),
    password: z.string().min(1, 'Password is required'),
});

export const registerSchema = z.object({
    email: z
        .string()
        .min(1, 'Email is required')
        .email('Please enter a valid email address')
        .max(100, 'Email cannot be longer than 100 characters'),
    password: z
        .string()
        .min(8, 'Password must be at least 8 characters long')
        .max(100, 'Password cannot be longer than 100 characters'),
    name: z
        .string()
        .min(1, 'Name is required')
        .min(2, 'Name must be at least 2 characters long')
        .max(100, 'Name cannot be longer than 100 characters'),
    surname: z
        .string()
        .min(2, 'Surname must be at least 2 characters long')
        .max(100, 'Surname cannot be longer than 100 characters')
        .optional(),
});

export type LoginFormData = z.infer<typeof loginSchema>;
export type RegisterFormData = z.infer<typeof registerSchema>;

