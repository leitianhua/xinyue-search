#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
密码生成脚本
用于生成加密后的密码和密码盐，然后可以手动复制到数据库中
"""

import hashlib
import random
import string

def generate_salt(length=4):
    """生成随机密码盐"""
    characters = string.ascii_letters + string.digits
    return ''.join(random.choice(characters) for _ in range(length))

def encode_password(password, salt):
    """密码加密函数，与PHP版本保持一致"""
    # 使用与PHP相同的加密方式: sha1(password . salt . password . salt)
    text = password + salt + password + salt
    return hashlib.sha1(text.encode('utf-8')).hexdigest()

def main():
    print("=== 密码生成工具 ===")
    print("此工具将生成加密后的密码和密码盐，可用于重置管理员密码\n")
    
    # 获取用户输入
    username = input("请输入管理员用户名 (默认: admin): ") or "leith"
    password = input("请输入新密码 (默认: admin123): ") or "leizaiwan566"
    
    # 生成密码盐
    salt = generate_salt()
    
    # 加密密码
    encrypted_password = encode_password(password, salt)
    
    # 显示结果
    print("\n=== 生成结果 ===")
    print(f"用户名: {username}")
    print(f"明文密码: {password}")
    print(f"密码盐: {salt}")
    print(f"加密后的密码: {encrypted_password}")
    
    # 显示SQL更新语句
    print("\n=== SQL更新语句 ===")
    print(f"UPDATE `qf_admin` SET `admin_password` = '{encrypted_password}', `admin_salt` = '{salt}' WHERE `admin_account` = '{username}';")
    
    # 显示使用说明
    print("\n=== 使用说明 ===")
    print("1. 登录您的数据库管理工具（如phpMyAdmin）")
    print("2. 选择您的数据库")
    print("3. 执行上面的SQL更新语句，或者手动更新qf_admin表中的相应字段")
    print("4. 更新完成后，您可以使用新密码登录系统")
    print("\n注意：请确保在操作前备份数据库，以防意外情况发生。")

if __name__ == "__main__":
    main()